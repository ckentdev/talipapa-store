@props([
    'variant' => 'marketplace',
])

@auth
    @php
        $user = auth()->user();
        $isApp = $variant === 'app';

        $profileLinks = match (true) {
            $user->isCustomer() => [
                ['label' => 'My orders', 'route' => 'customer.orders.index', 'icon' => 'ri-shopping-bag-3-line'],
                ['label' => 'Account', 'route' => 'customer.account', 'icon' => 'ri-user-settings-line'],
                ['label' => 'Addresses', 'route' => 'customer.addresses.index', 'icon' => 'ri-map-pin-line'],
            ],
            $user->isStoreOwner() => [
                ['label' => 'Dashboard', 'route' => 'store.dashboard', 'icon' => 'ri-dashboard-line'],
                ['label' => 'Customer Orders', 'route' => 'store.orders', 'icon' => 'ri-shopping-bag-3-line'],
                ['label' => 'Products & Inventory', 'route' => 'store.products', 'icon' => 'ri-shopping-basket-line'],
                ['label' => 'Account Settings', 'route' => 'store.account', 'icon' => 'ri-user-settings-line'],
                ['label' => 'Store Information', 'route' => 'store.information', 'icon' => 'ri-store-2-line'],
            ],
            $user->isRider() => [
                ['label' => 'Dashboard', 'route' => 'rider.dashboard', 'icon' => 'ri-dashboard-line'],
                ['label' => 'Deliveries', 'route' => 'rider.deliveries', 'icon' => 'ri-truck-line'],
                ['label' => 'Account', 'route' => 'rider.account', 'icon' => 'ri-user-settings-line'],
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

        $roleClasses = match (true) {
            $user->isAdmin() => 'bg-purple-100 text-purple-700',
            $user->isStoreOwner() => 'bg-forest-100 text-forest-700',
            $user->isRider() => 'bg-blue-100 text-blue-700',
            default => 'bg-brand-100 text-brand-700',
        };

        $storeName = $user->isStoreOwner() ? $user->storeProfile?->store_name : null;
    @endphp

    <div class="relative">
        <button
            id="header-profile-button"
            type="button"
            data-dropdown-toggle="header-profile-dropdown"
            data-dropdown-placement="bottom-end"
            @class([
                'flex items-center gap-2 rounded-full border transition focus:outline-none focus:ring-2 focus:ring-brand-200',
                'border-transparent py-1 pl-1 pr-2.5 hover:border-avocado-200 hover:bg-avocado-50 sm:pr-3' => ! $isApp,
                'border-avocado-200/70 bg-white py-1 pl-1 pr-2 shadow-sm hover:border-brand-200 hover:shadow md:pr-3' => $isApp,
            ])
            aria-label="Account menu"
        >
            <span @class([
                'flex items-center justify-center rounded-full font-semibold',
                'h-9 w-9 bg-brand-100 text-sm text-brand-700' => ! $isApp,
                'h-9 w-9 bg-gradient-to-br from-forest-600 to-brand-600 text-sm text-white ring-2 ring-white' => $isApp,
            ])>
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </span>

            @if ($isApp)
                <span class="hidden text-left md:block">
                    <span class="block max-w-[140px] truncate text-sm font-semibold leading-tight text-gray-900">{{ $user->name }}</span>
                    <span class="block max-w-[140px] truncate text-[11px] leading-tight text-gray-500">
                        {{ $storeName ?? $user->role->label() }}
                    </span>
                </span>
            @else
                <span class="hidden text-left md:block">
                    <span class="block max-w-[120px] truncate text-sm font-semibold leading-tight text-gray-900">{{ $user->name }}</span>
                    <span class="block text-[11px] leading-tight text-gray-500">{{ $user->role->label() }}</span>
                </span>
            @endif

            <i class="ri-arrow-down-s-line hidden text-lg text-gray-400 md:inline" aria-hidden="true"></i>
        </button>

        <div
            id="header-profile-dropdown"
            class="z-50 hidden w-72 divide-y divide-gray-100 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl"
        >
            <div @class([
                'px-4 py-4',
                'bg-gradient-to-r from-avocado-50/80 to-white' => $isApp,
            ])>
                <div class="flex items-center gap-3">
                    <span @class([
                        'flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-sm font-bold',
                        'bg-brand-100 text-brand-700' => ! $isApp,
                        'bg-gradient-to-br from-forest-600 to-brand-600 text-white' => $isApp,
                    ])>
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                        <p class="truncate text-xs text-gray-500">{{ $user->email }}</p>
                        <span @class(['mt-1.5 inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide', $roleClasses])>
                            {{ $user->role->label() }}
                        </span>
                    </div>
                </div>
                @if ($storeName)
                    <p class="mt-3 truncate rounded-lg bg-white/80 px-2.5 py-1.5 text-xs text-gray-600 ring-1 ring-avocado-200/70">
                        <i class="ri-store-2-line mr-1 text-forest-600" aria-hidden="true"></i>
                        {{ $storeName }}
                    </p>
                @endif
            </div>

            <ul class="py-1">
                @foreach ($profileLinks as $link)
                    <li>
                        <a
                            href="{{ route($link['route']) }}"
                            @class([
                                'flex items-center gap-3 px-4 py-2.5 text-sm transition',
                                'text-gray-700 hover:bg-avocado-50 hover:text-brand-700' => ! request()->routeIs($link['route'].'*'),
                                'bg-brand-50 font-medium text-brand-700' => request()->routeIs($link['route'].'*'),
                            ])
                        >
                            <i @class([$link['icon'], 'text-base', request()->routeIs($link['route'].'*') ? 'text-brand-600' : 'text-gray-400'])" aria-hidden="true"></i>
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="py-1">
                <a
                    href="{{ route('landing') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition hover:bg-avocado-50 hover:text-brand-700"
                >
                    <i class="ri-home-4-line text-base text-gray-400" aria-hidden="true"></i>
                    Back to marketplace
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm text-red-600 transition hover:bg-red-50"
                    >
                        <i class="ri-logout-box-r-line text-base" aria-hidden="true"></i>
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
@endauth
