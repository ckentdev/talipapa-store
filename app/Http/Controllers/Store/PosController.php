<?php

namespace App\Http\Controllers\Store;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\PosCheckoutRequest;
use App\Services\OrderService;
use App\Support\CheckoutPaymentSummary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    public function index(Request $request): View
    {
        $store = $request->user()->storeProfile;

        $products = $store->products()
            ->with('category')
            ->available()
            ->orderBy('name')
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'price' => (float) $product->price,
                'stock' => (int) $product->stock,
                'image_url' => $product->imageUrl(),
                'category' => $product->category?->name,
            ]);

        $hasStoreAddress = $store->addresses()->exists();

        return view('store.pos.index', compact('store', 'products', 'hasStoreAddress'));
    }

    public function checkout(PosCheckoutRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $store = $request->user()->storeProfile;
        $paymentMethod = PaymentMethod::tryFromStored($validated['payment_method'])
            ?? PaymentMethod::Cash;

        $paymentNote = CheckoutPaymentSummary::orderNote([
            'payment_method' => $validated['payment_method'],
            'ewallet_provider' => $validated['ewallet_provider'] ?? null,
            'payment' => [
                'holder' => $validated['card_holder_name'] ?? null,
                'last_four' => isset($validated['card_number'])
                    ? substr($validated['card_number'], -4)
                    : null,
            ],
        ]);

        $order = $this->orderService->createPosSale(
            $store,
            $request->user(),
            $validated['items'],
            $paymentMethod,
            $validated['customer_name'] ?? null,
            $paymentNote,
            (string) ($validated['discount_type'] ?? 'none'),
            isset($validated['discount_value']) ? (float) $validated['discount_value'] : 0.0,
            isset($validated['cash_tendered']) ? (float) $validated['cash_tendered'] : null,
        );

        return redirect()
            ->route('store.pos.index')
            ->with('success', "Sale completed. Order {$order->order_number} recorded.");
    }
}
