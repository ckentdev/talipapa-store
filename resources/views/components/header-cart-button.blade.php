@props([
    'cartCount' => 0,
])

<button
    type="button"
    data-cart-trigger
    data-dropdown-toggle="header-cart-dropdown"
    data-dropdown-placement="bottom-end"
    class="relative flex h-10 w-10 items-center justify-center rounded-full text-forest-600 transition hover:bg-avocado-50 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-200"
    aria-label="Cart"
    aria-controls="header-cart-dropdown"
>
    <i class="ri-shopping-cart-2-line text-xl" aria-hidden="true"></i>
    <span
        data-cart-badge
        @class([
            'absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white',
            'hidden' => $cartCount <= 0,
        ])
    >
        @if ($cartCount > 0)
            {{ $cartCount > 9 ? '9+' : $cartCount }}
        @endif
    </span>
</button>
