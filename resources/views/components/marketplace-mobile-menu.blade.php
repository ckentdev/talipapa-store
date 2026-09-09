@php
    $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;

    $primaryLinks = [
        ['label' => 'Home', 'route' => 'landing', 'icon' => 'ri-home-4-line', 'patterns' => ['landing']],
        ['label' => 'Shop', 'route' => 'products.index', 'icon' => 'ri-shopping-basket-line', 'patterns' => ['products.*']],
        ['label' => 'Stores', 'route' => 'stores.index', 'icon' => 'ri-store-2-line', 'patterns' => ['stores.*']],
    ];

    $accountLinks = [];

    if (auth()->check()) {
        $user = auth()->user();

        $accountLinks = match (true) {
            $user->isCustomer() => [
                ['label' => 'My orders', 'route' => 'customer.orders.index', 'icon' => 'ri-shopping-bag-3-line'],
                ['label' => 'Account', 'route' => 'customer.account', 'icon' => 'ri-user-settings-line'],
                ['label' => 'Addresses', 'route' => 'customer.addresses.index', 'icon' => 'ri-map-pin-line'],
            ],
            $user->isStoreOwner() => [
                ['label' => 'Dashboard', 'route' => 'store.dashboard', 'icon' => 'ri-dashboard-line'],
                ['label' => 'Customer Orders', 'route' => 'store.orders', 'icon' => 'ri-shopping-bag-3-line'],
                ['label' => 'Products', 'route' => 'store.products', 'icon' => 'ri-shopping-basket-line'],
            ],
            $user->isRider() => [
                ['label' => 'Dashboard', 'route' => 'rider.dashboard', 'icon' => 'ri-dashboard-line'],
                ['label' => 'Deliveries', 'route' => 'rider.deliveries', 'icon' => 'ri-truck-line'],
            ],
            $user->isAdmin() => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'ri-dashboard-line'],
                ['label' => 'Customer Orders', 'route' => 'admin.orders', 'icon' => 'ri-shopping-bag-3-line'],
                ['label' => 'Settings', 'route' => 'admin.settings', 'icon' => 'ri-settings-3-line'],
            ],
            default => [
                ['label' => 'Account', 'route' => 'account.show', 'icon' => 'ri-user-settings-line'],
            ],
        };
    }
@endphp

<div
    id="marketplace-mobile-menu"
    class="fixed left-0 top-0 z-50 h-screen w-[min(100vw,20rem)] -translate-x-full overflow-y-auto border-r border-avocado-100/80 bg-white transition-transform"
    tabindex="-1"
    aria-labelledby="marketplace-mobile-menu-title"
>
    <div class="flex h-full flex-col">
        <div class="flex items-center justify-between border-b border-avocado-100/80 px-4 py-4">
            <div id="marketplace-mobile-menu-title">
                <x-talipapa-logo size="sm" />
            </div>
            <button
                type="button"
                data-drawer-hide="marketplace-mobile-menu"
                aria-controls="marketplace-mobile-menu"
                class="inline-flex h-10 w-10 items-center justify-center rounded-full text-gray-500 transition hover:bg-avocado-50 hover:text-forest-600"
            >
                <span class="sr-only">Close menu</span>
                <i class="ri-close-line text-2xl" aria-hidden="true"></i>
            </button>
        </div>

        <div class="border-b border-avocado-100/80 px-4 py-4">
            <x-header-search />
        </div>

        <nav class="flex-1 overflow-y-auto px-2 py-3" aria-label="Mobile menu">
            <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">Browse</p>
            <ul class="space-y-1">
                @foreach ($primaryLinks as $link)
                    @php $active = request()->routeIs($link['patterns']); @endphp
                    <li>
                        <a
                            href="{{ route($link['route']) }}"
                            data-drawer-hide="marketplace-mobile-menu"
                            @class([
                                'flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition',
                                'bg-forest-600/10 text-forest-600' => $active,
                                'text-gray-700 hover:bg-avocado-50 hover:text-forest-600' => ! $active,
                            ])
                        >
                            <i @class([$link['icon'], 'text-lg', $active ? 'text-forest-600' : 'text-gray-400'])" aria-hidden="true"></i>
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            @auth
                <p class="mt-5 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">Account</p>

                <div class="mx-3 mb-3 rounded-xl border border-avocado-100 bg-avocado-50/60 px-3 py-3">
                    <p class="truncate text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-gray-500">{{ auth()->user()->email }}</p>
                </div>

                <ul class="space-y-1">
                    <li>
                        <a
                            href="{{ route('notifications.index') }}"
                            data-drawer-hide="marketplace-mobile-menu"
                            class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-gray-700 transition hover:bg-avocado-50 hover:text-forest-600"
                        >
                            <span class="relative">
                                <i class="ri-notification-3-line text-lg text-gray-400" aria-hidden="true"></i>
                                @if ($unreadCount > 0)
                                    <span class="absolute -right-1.5 -top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                    </span>
                                @endif
                            </span>
                            Notifications
                            @if ($unreadCount > 0)
                                <span class="ml-auto rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white">
                                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                </span>
                            @endif
                        </a>
                    </li>

                    @foreach ($accountLinks as $link)
                        <li>
                            <a
                                href="{{ route($link['route']) }}"
                                data-drawer-hide="marketplace-mobile-menu"
                                @class([
                                    'flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition',
                                    'bg-forest-600/10 text-forest-600' => request()->routeIs($link['route'].'*'),
                                    'text-gray-700 hover:bg-avocado-50 hover:text-forest-600' => ! request()->routeIs($link['route'].'*'),
                                ])
                            >
                                <i @class([$link['icon'], 'text-lg', request()->routeIs($link['route'].'*') ? 'text-forest-600' : 'text-gray-400'])" aria-hidden="true"></i>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-5 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">Account</p>
                <ul class="space-y-1 px-1">
                    <li>
                        <a
                            href="{{ route('login') }}"
                            data-drawer-hide="marketplace-mobile-menu"
                            class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-gray-700 transition hover:bg-avocado-50 hover:text-forest-600"
                        >
                            <i class="ri-login-box-line text-lg text-gray-400" aria-hidden="true"></i>
                            Sign in
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('join') }}"
                            data-drawer-hide="marketplace-mobile-menu"
                            class="mx-2 flex items-center justify-center gap-2 rounded-xl bg-accent-500 px-4 py-3 text-sm font-bold text-gray-900 transition hover:bg-accent-600"
                        >
                            <i class="ri-user-add-line text-lg" aria-hidden="true"></i>
                            Create account
                        </a>
                    </li>
                </ul>
            @endauth
        </nav>

        @auth
            <div class="border-t border-avocado-100/80 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 px-4 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                    >
                        <i class="ri-logout-box-r-line text-lg" aria-hidden="true"></i>
                        Sign out
                    </button>
                </form>
            </div>
        @endauth
    </div>
</div>
