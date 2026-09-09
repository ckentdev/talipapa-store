@php
    use App\Services\CartService;

    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart(auth()->user(), session()->getId());
    $cart->load(['items.product.store']);
    $cartCount = $cartService->getItemCount($cart);
    $cartSubtotal = $cart->subtotal();
    $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
@endphp

<header class="sticky top-0 z-50 border-b border-avocado-100/80 bg-white/95 shadow-sm shadow-forest-600/5 backdrop-blur-md">
    {{-- Mobile: menu toggle + logo + cart --}}
    <div class="mx-auto grid max-w-7xl grid-cols-[2.5rem_1fr_2.5rem] items-center gap-2 px-4 py-3 lg:hidden">
        <button
            type="button"
            data-drawer-target="marketplace-mobile-menu"
            data-drawer-toggle="marketplace-mobile-menu"
            aria-controls="marketplace-mobile-menu"
            class="relative inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-forest-600 transition hover:bg-avocado-50 focus:outline-none focus:ring-2 focus:ring-brand-200"
        >
            <span class="sr-only">Open menu</span>
            <i class="ri-menu-line text-2xl" aria-hidden="true"></i>
            @if ($unreadCount > 0)
                <span
                    id="mobile-menu-notification-badge"
                    class="absolute right-0 top-0 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold leading-none text-white ring-2 ring-white"
                >
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @else
                <span
                    id="mobile-menu-notification-badge"
                    class="absolute right-0 top-0 hidden h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold leading-none text-white ring-2 ring-white"
                ></span>
            @endif
        </button>

        <x-talipapa-logo size="sm" class="justify-self-center" />

        <div class="justify-self-end">
            <x-header-cart-button :cart-count="$cartCount" />
        </div>
    </div>

    {{-- Desktop --}}
    <div class="mx-auto hidden max-w-7xl flex-wrap items-center gap-x-4 gap-y-4 px-4 py-3 sm:gap-x-6 sm:px-6 lg:flex lg:flex-nowrap lg:gap-x-8 lg:px-8">
        <x-talipapa-logo class="order-1 shrink-0" />

        <div class="order-3 flex w-full min-w-0 flex-1 items-center lg:order-2 lg:w-auto lg:px-2 xl:px-4">
            <x-header-search />
        </div>

        <div class="order-2 ml-auto flex shrink-0 items-center gap-2 sm:gap-3 lg:order-3 lg:ml-0 lg:pl-2 xl:pl-4">
            @auth
                @include('layouts.partials._notification_bell')

                <x-header-cart-button :cart-count="$cartCount" />

                <x-header-profile-dropdown />
            @else
                <x-header-cart-button :cart-count="$cartCount" />

                <a href="{{ route('login') }}" class="hidden text-sm font-medium text-forest-600 hover:text-brand-600 sm:inline">Login</a>
                <a href="{{ route('join') }}" class="rounded-full bg-accent-500 px-4 py-2 text-sm font-semibold text-gray-900 transition hover:bg-accent-600">Register</a>
            @endauth
        </div>
    </div>

    <x-header-cart-dropdown
        :cart="$cart"
        :cart-count="$cartCount"
        :cart-subtotal="$cartSubtotal"
    />

    <x-marketplace-mobile-menu />
</header>
