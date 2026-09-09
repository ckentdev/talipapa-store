@props([
    'cart',
    'checkout' => [],
    'addresses',
    'deliveryFee',
    'step',
])

@php
    $selectedAddress = $addresses->firstWhere('id', $checkout['address_id'] ?? null);
    $itemCount = $cart->items->sum('quantity');
    $subtotal = $cart->subtotal();
    $total = $subtotal + $deliveryFee;
@endphp

<aside {{ $attributes->merge(['class' => 'lg:sticky lg:top-6']) }}>
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-5 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                    <i class="ri-receipt-line text-lg" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Order Summary</h2>
                    <p class="text-base text-gray-500">{{ $itemCount }} {{ str('item')->plural($itemCount) }}</p>
                </div>
            </div>
        </div>

        <div class="max-h-64 space-y-3 overflow-y-auto px-5 py-4 sm:px-6">
            @foreach ($cart->items as $item)
                <div class="flex gap-3">
                    <x-product-image
                        :product="$item->product"
                        class="h-14 w-14 shrink-0 rounded-lg bg-gray-100 object-cover ring-1 ring-gray-100"
                    />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-base font-medium text-gray-900">{{ $item->product->name }}</p>
                        <p class="text-base text-gray-500">{{ $item->product->store?->store_name }}</p>
                        <p class="mt-0.5 text-base text-gray-600">Qty {{ $item->quantity }} · ₱{{ number_format($item->unit_price, 2) }}</p>
                    </div>
                    <p class="shrink-0 text-base font-semibold text-gray-900">₱{{ number_format($item->total(), 2) }}</p>
                </div>
            @endforeach
        </div>

        <div class="space-y-2 border-t border-gray-100 px-5 py-4 text-base sm:px-6">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span class="font-medium text-gray-900">₱{{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Delivery fee</span>
                <span class="font-medium text-gray-900">₱{{ number_format($deliveryFee, 2) }}</span>
            </div>
            <div class="flex justify-between border-t border-gray-100 pt-3 text-base font-bold text-gray-900">
                <span>Total</span>
                <span class="text-brand-600">₱{{ number_format($total, 2) }}</span>
            </div>
        </div>

        @if ($step >= 2 && ! empty($checkout['customer']))
            <div class="border-t border-gray-100 px-5 py-4 sm:px-6">
                <p class="mb-2 text-base font-bold uppercase tracking-wider text-gray-400">Contact</p>
                <p class="text-base font-medium text-gray-900">{{ $checkout['customer']['name'] ?? auth()->user()->name }}</p>
                <p class="text-base text-gray-600">{{ $checkout['customer']['phone'] ?? auth()->user()->phone }}</p>
            </div>
        @endif

        @if ($step >= 3 && $selectedAddress)
            <div class="border-t border-gray-100 px-5 py-4 sm:px-6">
                <p class="mb-2 text-base font-bold uppercase tracking-wider text-gray-400">Deliver to</p>
                <p class="text-base font-medium text-gray-900">{{ $selectedAddress->label ?? 'Address' }}</p>
                <p class="mt-0.5 text-base text-gray-600">{{ $selectedAddress->fullAddress() }}</p>
            </div>
        @endif

        @if ($step >= 4 && ! empty($checkout['payment_method']))
            <div class="border-t border-gray-100 px-5 py-4 sm:px-6">
                <p class="mb-2 text-base font-bold uppercase tracking-wider text-gray-400">Payment</p>
                <p class="text-base font-medium text-gray-900">{{ \App\Support\CheckoutPaymentSummary::label($checkout) }}</p>
            </div>
        @endif

        @if ($step === 5)
            <div class="border-t border-gray-100 bg-avocado-50/50 px-5 py-3 sm:px-6">
                <p class="flex items-start gap-2 text-base text-gray-600">
                    <i class="ri-shield-check-line mt-0.5 shrink-0 text-brand-600" aria-hidden="true"></i>
                    Review your details below before placing your order.
                </p>
            </div>
        @endif
    </div>
</aside>
