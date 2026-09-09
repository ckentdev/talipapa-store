<?php

namespace App\Services\VoiceAssistant;

use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use App\Http\Concerns\ResolvesCart;
use App\Models\Address;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class VoiceActionService
{
    use ResolvesCart;

    public function __construct(
        private readonly CartService $cartService,
    ) {}

    /**
     * @param  array<string, mixed>  $nlp
     * @return array{success: bool, message?: string, requires_login?: bool, redirect?: string}
     */
    public function apply(Request $request, array $nlp, Collection $products): array
    {
        $intent = (string) ($nlp['intent'] ?? 'product_search');

        if ($intent === 'product_search' || $intent === 'general_chat') {
            return ['success' => true];
        }

        if (! $request->user()) {
            return [
                'success' => false,
                'requires_login' => true,
                'message' => 'Please sign in to update your cart or checkout.',
            ];
        }

        $user = $request->user();

        if ($user->role !== UserRole::Customer) {
            return [
                'success' => false,
                'message' => 'Voice checkout is available for customer accounts only.',
            ];
        }

        return match ($intent) {
            'add_to_cart' => $this->addToCart($request, $nlp, $products),
            'set_payment_method' => $this->setPaymentMethod($request, $nlp),
            'set_delivery_note' => $this->setDeliveryNote($request, $nlp),
            'select_address' => $this->selectAddress($request, $nlp, $user),
            'go_to_checkout_step' => $this->goToCheckoutStep($request, $nlp),
            default => ['success' => true],
        };
    }

    /**
     * @param  array<string, mixed>  $nlp
     * @param  Collection<int, Product>  $products
     * @return array{success: bool, message?: string, requires_login?: bool}
     */
    private function addToCart(Request $request, array $nlp, Collection $products): array
    {
        $checkout = is_array($nlp['checkout'] ?? null) ? $nlp['checkout'] : [];
        $productId = (int) ($checkout['product_id'] ?? 0);
        $quantity = max(1, (int) ($nlp['quantity'] ?? 1));

        if ($productId <= 0) {
            $product = $products->first();
        } else {
            $product = $products->firstWhere('id', $productId) ?? Product::query()->available()->find($productId);
        }

        if ($product === null) {
            return [
                'success' => false,
                'message' => 'I could not find that product to add. Please pick from the results.',
            ];
        }

        $cart = $this->resolveCart();
        $this->cartService->addItem($cart, $product, $quantity);

        return [
            'success' => true,
            'message' => "Added {$quantity} × {$product->name} to your cart.",
        ];
    }

    /**
     * @param  array<string, mixed>  $nlp
     * @return array{success: bool, message?: string}
     */
    private function setPaymentMethod(Request $request, array $nlp): array
    {
        $checkout = is_array($nlp['checkout'] ?? null) ? $nlp['checkout'] : [];
        $method = PaymentMethod::tryFromStored((string) ($checkout['payment_method'] ?? 'cod'));

        if ($method === null) {
            $method = PaymentMethod::Cod;
        }

        if ($method === PaymentMethod::Card) {
            return [
                'success' => false,
                'message' => 'For card payment, please enter your card details on the checkout page.',
            ];
        }

        $session = $request->session()->get('checkout', []);
        $session['payment_method'] = $method->value;
        $request->session()->put('checkout', $session);

        return [
            'success' => true,
            'message' => 'Payment set to '.$method->label().'.',
        ];
    }

    /**
     * @param  array<string, mixed>  $nlp
     * @return array{success: bool, message?: string}
     */
    private function setDeliveryNote(Request $request, array $nlp): array
    {
        $checkout = is_array($nlp['checkout'] ?? null) ? $nlp['checkout'] : [];
        $notes = trim((string) ($checkout['notes'] ?? $nlp['original_text'] ?? ''));

        if ($notes === '') {
            return ['success' => false, 'message' => 'I did not catch the delivery note. Please try again.'];
        }

        $session = $request->session()->get('checkout', []);
        $session['notes'] = Str::limit($notes, 500);
        $request->session()->put('checkout', $session);

        return [
            'success' => true,
            'message' => 'Delivery note saved.',
        ];
    }

    /**
     * @param  array<string, mixed>  $nlp
     * @return array{success: bool, message?: string, redirect?: string}
     */
    private function selectAddress(Request $request, array $nlp, User $user): array
    {
        $checkout = is_array($nlp['checkout'] ?? null) ? $nlp['checkout'] : [];
        $addresses = $user->addresses()->get();

        if ($addresses->isEmpty()) {
            return [
                'success' => false,
                'message' => 'You have no saved addresses. Please add one first.',
                'redirect' => route('customer.addresses.create'),
            ];
        }

        $address = null;

        if (isset($checkout['address_index'])) {
            $address = $addresses->values()->get((int) $checkout['address_index']);
        }

        if ($address === null && ! empty($checkout['address_label'])) {
            $label = Str::lower((string) $checkout['address_label']);
            $address = $addresses->first(fn (Address $a) => Str::lower((string) $a->label) === $label);
        }

        $address ??= $addresses->firstWhere('is_default', true) ?? $addresses->first();

        $session = $request->session()->get('checkout', []);
        $session['address_id'] = $address->id;
        $request->session()->put('checkout', $session);

        return [
            'success' => true,
            'message' => 'Delivery address set to '.($address->label ?? 'your address').'.',
        ];
    }

    /**
     * @param  array<string, mixed>  $nlp
     * @return array{success: bool, message?: string, redirect?: string}
     */
    private function goToCheckoutStep(Request $request, array $nlp): array
    {
        $checkout = is_array($nlp['checkout'] ?? null) ? $nlp['checkout'] : [];
        $step = max(1, min(5, (int) ($checkout['step'] ?? 1)));

        return [
            'success' => true,
            'message' => 'Opening checkout step '.$step.'.',
            'redirect' => route('customer.checkout.show', ['step' => $step]),
        ];
    }
}
