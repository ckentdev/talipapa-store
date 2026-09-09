@props([
    'cart',
    'itemCount',
])

@php
    $subtotal = $cart->subtotal();
@endphp

<aside {{ $attributes->merge(['class' => 'lg:sticky lg:top-6']) }}>
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-5 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                    <i class="ri-shopping-cart-2-line text-lg" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Order Summary</h2>
                    <p class="text-base text-gray-500">{{ $itemCount }} {{ str('item')->plural($itemCount) }}</p>
                </div>
            </div>
        </div>

        <div class="max-h-72 space-y-3 overflow-y-auto px-5 py-4 sm:px-6">
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
                <span>Subtotal ({{ $itemCount }} {{ str('item')->plural($itemCount) }})</span>
                <span class="font-medium text-gray-900">₱{{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between border-t border-gray-100 pt-3 text-lg font-bold text-gray-900">
                <span>Estimated total</span>
                <span class="text-brand-600">₱{{ number_format($subtotal, 2) }}</span>
            </div>
            <p class="text-base text-gray-500">Delivery fee calculated at checkout.</p>
        </div>

        <div class="border-t border-gray-100 px-5 py-4 sm:px-6">
            @auth
                <a
                    href="{{ route('customer.checkout.show') }}"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand-600 px-6 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-brand-700"
                >
                    Proceed to checkout
                    <i class="ri-arrow-right-line" aria-hidden="true"></i>
                </a>
            @else
                <p class="rounded-xl border border-brand-100 bg-brand-50/50 px-4 py-3 text-base text-gray-700">
                    Sign in or create an account to complete your order.
                </p>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-base font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        Sign in
                    </a>
                    <a
                        href="{{ route('join') }}"
                        class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-4 py-2.5 text-base font-semibold text-white transition hover:bg-brand-700"
                    >
                        Join
                    </a>
                </div>
            @endauth

            <a
                href="{{ route('products.index') }}"
                class="mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-base font-medium text-gray-700 transition hover:bg-gray-50"
            >
                <i class="ri-store-2-line" aria-hidden="true"></i>
                Continue shopping
            </a>
        </div>
    </div>
</aside>
