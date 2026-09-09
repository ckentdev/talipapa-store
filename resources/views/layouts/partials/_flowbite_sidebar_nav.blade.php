@props(['items' => []])

@php
    $remixIcons = [
        'home' => 'ri-home-4-line',
        'cart' => 'ri-shopping-cart-2-line',
        'orders' => 'ri-shopping-bag-3-line',
        'account' => 'ri-user-settings-line',
        'dashboard' => 'ri-dashboard-line',
        'products' => 'ri-shopping-basket-line',
        'riders' => 'ri-riding-line',
        'deliveries' => 'ri-truck-line',
        'earnings' => 'ri-wallet-3-line',
        'users' => 'ri-group-line',
        'approvals' => 'ri-shield-check-line',
        'voc' => 'ri-chat-voice-line',
        'settings' => 'ri-settings-3-line',
    ];
@endphp

<ul class="space-y-1 font-medium">
    @foreach ($items as $item)
        @php
            $active = request()->routeIs($item['patterns']);
            $iconClass = str_starts_with($item['icon'], 'ri-')
                ? $item['icon']
                : ($remixIcons[$item['icon']] ?? 'ri-circle-line');
        @endphp
        <li>
            <a
                href="{{ route($item['route']) }}"
                data-drawer-hide="talipapa-sidebar"
                @class([
                    'group flex items-center rounded-lg p-2 text-base font-medium transition-colors',
                    'bg-forest-50 text-forest-700' => $active,
                    'text-gray-700 hover:bg-gray-100' => ! $active,
                ])
            >
                <i
                    @class([
                        $iconClass,
                        'h-5 w-5 shrink-0 transition duration-75',
                        'text-forest-600' => $active,
                        'text-gray-400 group-hover:text-forest-600' => ! $active,
                    ])
                    aria-hidden="true"
                ></i>
                <span class="ms-3 flex min-w-0 flex-1 items-center justify-between gap-2 whitespace-nowrap">
                    <span class="truncate">{{ $item['label'] }}</span>
                    @if (! empty($item['badge']) && ($item['badgeType'] ?? null) === 'online')
                        <x-nav-online-badge :count="$item['badge']" />
                    @elseif (! empty($item['badge']) && ($item['badgeType'] ?? null) === 'pending')
                        <x-nav-pending-badge :count="$item['badge']" />
                    @elseif (! empty($item['badge']))
                        <span class="inline-flex min-w-[1.25rem] shrink-0 items-center justify-center rounded-full bg-green-500 px-1.5 py-0.5 text-[10px] font-bold text-white">
                            {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                        </span>
                    @endif
                </span>
            </a>
        </li>
    @endforeach
</ul>
