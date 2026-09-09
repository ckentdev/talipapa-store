@php
    $sizeClass = ($mobile ?? false) ? 'text-lg' : 'text-base';
    $inverted = $inverted ?? false;

    if ($inverted) {
        $colorClass = 'text-white';
    } else {
        $colorClass = ($active ?? false) ? 'text-forest-600' : 'text-gray-400';
    }

    $remixIcons = [
        'home' => ['line' => 'ri-home-4-line', 'fill' => 'ri-home-4-fill'],
        'cart' => ['line' => 'ri-shopping-cart-2-line', 'fill' => 'ri-shopping-cart-2-fill'],
        'orders' => ['line' => 'ri-shopping-bag-3-line', 'fill' => 'ri-shopping-bag-3-fill'],
        'account' => ['line' => 'ri-user-3-line', 'fill' => 'ri-user-3-fill'],
        'store' => ['line' => 'ri-store-2-line', 'fill' => 'ri-store-2-fill'],
        'dashboard' => ['line' => 'ri-dashboard-line', 'fill' => 'ri-dashboard-fill'],
        'products' => ['line' => 'ri-shopping-basket-line', 'fill' => 'ri-shopping-basket-2-fill'],
        'pos' => ['line' => 'ri-cash-line', 'fill' => 'ri-cash-fill'],
        'riders' => ['line' => 'ri-riding-line', 'fill' => 'ri-riding-fill'],
        'deliveries' => ['line' => 'ri-truck-line', 'fill' => 'ri-truck-fill'],
        'earnings' => ['line' => 'ri-wallet-3-line', 'fill' => 'ri-wallet-3-fill'],
        'users' => ['line' => 'ri-group-line', 'fill' => 'ri-group-fill'],
        'approvals' => ['line' => 'ri-shield-check-line', 'fill' => 'ri-shield-check-fill'],
        'settings' => ['line' => 'ri-settings-3-line', 'fill' => 'ri-settings-3-fill'],
    ];

    if (str_starts_with($icon, 'ri-')) {
        $iconClass = $icon;
    } else {
        $variants = $remixIcons[$icon] ?? ['line' => 'ri-circle-line', 'fill' => 'ri-circle-fill'];
        $iconClass = (($active ?? false) || $inverted) ? $variants['fill'] : $variants['line'];
    }
@endphp

<i class="{{ $iconClass }} {{ $sizeClass }} {{ $colorClass }}" aria-hidden="true"></i>
