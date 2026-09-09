@props([
    'cart',
    'cartCount' => 0,
    'cartSubtotal' => 0,
])

{{-- Single shared dropdown panel for all cart triggers in the header --}}
<div
    id="header-cart-dropdown"
    class="z-50 hidden w-80 divide-y divide-gray-100 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg sm:w-96"
>
    <div id="header-cart-dropdown-content">
        <x-partials.header-cart-dropdown-content
            :cart="$cart"
            :cart-count="$cartCount"
            :cart-subtotal="$cartSubtotal"
        />
    </div>
</div>
