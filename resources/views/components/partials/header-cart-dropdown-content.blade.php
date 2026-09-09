@props([
    'cart',
    'cartCount' => 0,
    'cartSubtotal' => 0,
])

<div class="flex items-center justify-between px-4 py-3">
    <p class="text-sm font-semibold text-gray-900">Your cart</p>
    <p class="text-xs text-gray-500" data-cart-item-label>{{ $cartCount }} {{ Str::plural('item', $cartCount) }}</p>
</div>

@if ($cart->items->isEmpty())
    <div class="px-4 py-8 text-center">
        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-avocado-50 text-forest-600">
            <i class="ri-shopping-cart-2-line text-xl" aria-hidden="true"></i>
        </div>
        <p class="text-sm text-gray-500">Your cart is empty.</p>
        <a href="{{ route('products.index') }}" class="mt-3 inline-flex text-sm font-semibold text-brand-600 hover:text-brand-700">
            Browse products
        </a>
    </div>
@else
    <div class="max-h-72 overflow-y-auto" data-cart-items>
        @foreach ($cart->items->take(5) as $item)
            <div class="flex gap-3 px-4 py-3 hover:bg-gray-50">
                <x-product-image :product="$item->product" class="h-14 w-14 shrink-0 rounded-lg bg-gray-100 object-cover" />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-gray-900">{{ $item->product->name }}</p>
                    <p class="truncate text-xs text-gray-500">{{ $item->product->store?->store_name }}</p>
                    <p class="mt-1 text-xs text-gray-600">
                        {{ $item->quantity }} x ₱{{ number_format($item->unit_price, 2) }}
                    </p>
                </div>
                <p class="shrink-0 text-sm font-semibold text-gray-900">₱{{ number_format($item->total(), 2) }}</p>
            </div>
        @endforeach

        @if ($cart->items->count() > 5)
            <p class="px-4 pb-3 text-xs text-gray-500" data-cart-more-count>
                + {{ $cart->items->count() - 5 }} more {{ Str::plural('item', $cart->items->count() - 5) }}
            </p>
        @endif
    </div>

    <div class="px-4 py-3">
        <div class="mb-3 flex items-center justify-between text-sm">
            <span class="text-gray-600">Subtotal</span>
            <span class="font-bold text-gray-900" data-cart-subtotal>₱{{ number_format($cartSubtotal, 2) }}</span>
        </div>
        <a
            href="{{ route('cart.index') }}"
            class="block w-full rounded-lg border border-gray-200 px-4 py-2.5 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
        >
            View full cart
        </a>
    </div>
@endif
