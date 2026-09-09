<?php

namespace App\Http\Controllers\Customer;

use App\Enums\EWalletProvider;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Concerns\ResolvesCart;
use App\Http\Requests\Checkout\ProcessCheckoutRequest;
use App\Models\Address;
use App\Services\CartService;
use App\Services\OrderService;
use App\Support\CheckoutPaymentSummary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    use ResolvesCart;

    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService,
    ) {}

    public function show(Request $request): View|RedirectResponse
    {
        $step = max(1, min(5, (int) ($request->route('step') ?? $request->query('step', 1))));
        $cart = $this->resolveCart()->load('items.product.store');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $checkout = $request->session()->get('checkout', []);
        if (($checkout['payment_method'] ?? null) === 'gcash') {
            $checkout['payment_method'] = PaymentMethod::EWallet->value;
            $request->session()->put('checkout', $checkout);
        }
        $addresses = $request->user()->addresses()->with(['region', 'province', 'city', 'barangay'])->get();
        $deliveryFee = $this->orderService->calculateDeliveryFee();

        return view('customer.checkout', compact('step', 'cart', 'checkout', 'addresses', 'deliveryFee'));
    }

    public function process(ProcessCheckoutRequest $request): RedirectResponse
    {
        $step = (int) $request->input('step', 1);
        $checkout = $request->session()->get('checkout', []);

        if ($step === 1) {
            return redirect()->route('customer.checkout.show', ['step' => 2]);
        }

        if ($step === 2) {
            $checkout['customer'] = $request->only(['name', 'phone']);
            $request->user()->update([
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
            ]);
            $request->session()->put('checkout', $checkout);

            return redirect()->route('customer.checkout.show', ['step' => 3]);
        }

        if ($step === 3) {
            $address = Address::query()
                ->where('user_id', $request->user()->id)
                ->findOrFail($request->integer('address_id'));

            $checkout['address_id'] = $address->id;

            if ($request->filled('notes')) {
                $checkout['notes'] = $request->string('notes')->toString();
            }

            $request->session()->put('checkout', $checkout);

            return redirect()->route('customer.checkout.show', ['step' => 4]);
        }

        if ($step === 4) {
            $paymentMethod = $request->input('payment_method');
            $checkout['payment_method'] = $paymentMethod;
            unset($checkout['ewallet_provider'], $checkout['payment']);

            if ($paymentMethod === PaymentMethod::EWallet->value) {
                $checkout['ewallet_provider'] = $request->input('ewallet_provider');
            } elseif ($paymentMethod === PaymentMethod::Card->value) {
                $cardNumber = preg_replace('/\D/', '', (string) $request->input('card_number'));
                $checkout['payment'] = [
                    'holder' => $request->input('card_holder_name'),
                    'last_four' => substr($cardNumber, -4),
                    'expiry' => $request->input('card_expiry'),
                ];
            }

            $request->session()->put('checkout', $checkout);

            return redirect()->route('customer.checkout.show', ['step' => 5]);
        }

        $cart = $this->resolveCart()->load('items.product');
        $address = Address::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($checkout['address_id'] ?? 0);

        $paymentNote = CheckoutPaymentSummary::orderNote($checkout);
        $orderNotes = collect([$checkout['notes'] ?? null, $paymentNote])->filter()->implode("\n");

        $order = $this->orderService->createFromCart(
            $cart,
            $request->user(),
            $address,
            PaymentMethod::tryFromStored($checkout['payment_method'] ?? PaymentMethod::Cod->value) ?? PaymentMethod::Cod,
            $orderNotes ?: null,
        );

        \App\Events\OrderStatusChanged::dispatch($order, \App\Enums\OrderStatus::Pending, \App\Enums\OrderStatus::Pending);

        $request->session()->forget('checkout');

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', 'Order placed successfully!');
    }
}
