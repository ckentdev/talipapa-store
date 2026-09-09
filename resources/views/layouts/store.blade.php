@extends('layouts.marketplace')

@php
    use App\Enums\OrderStatus;
    use App\Models\Order;
    use App\Models\User;

    $availableRidersCount = User::query()
        ->where('role', 'rider')
        ->whereHas('riderProfile', fn ($q) => $q->approved()->available())
        ->count();

    $pendingOrdersCount = auth()->user()->storeProfile
        ? Order::query()
            ->where('store_profile_id', auth()->user()->storeProfile->id)
            ->where('status', OrderStatus::Pending)
            ->count()
        : 0;

    $storeNav = [
        ['label' => 'Dashboard', 'mobileLabel' => 'Home', 'route' => 'store.dashboard', 'icon' => 'dashboard', 'patterns' => ['store.dashboard'], 'mobileTab' => true],
        [
            'label' => 'Customer Orders',
            'mobileLabel' => 'Orders',
            'route' => 'store.orders',
            'icon' => 'orders',
            'patterns' => ['store.orders*'],
            'badge' => $pendingOrdersCount > 0 ? $pendingOrdersCount : null,
            'badgeType' => 'pending',
            'mobileTab' => true,
        ],
        ['label' => 'Products & Inventory', 'mobileLabel' => 'Products', 'route' => 'store.products', 'icon' => 'products', 'patterns' => ['store.products*'], 'mobileTab' => true],
        ['label' => 'In App Point of Sales', 'mobileLabel' => 'POS', 'route' => 'store.pos.index', 'icon' => 'pos', 'patterns' => ['store.pos*'], 'mobileTab' => true],
        [
            'label' => 'Available Riders',
            'mobileLabel' => 'Riders',
            'route' => 'store.riders',
            'icon' => 'riders',
            'patterns' => ['store.riders*'],
            'badge' => $availableRidersCount > 0 ? $availableRidersCount : null,
            'badgeType' => 'online',
        ],
        ['label' => 'Account Settings', 'mobileLabel' => 'Settings', 'route' => 'store.account', 'icon' => 'account', 'patterns' => ['store.account*']],
        ['label' => 'Store Information', 'mobileLabel' => 'Store', 'route' => 'store.information', 'icon' => 'store', 'patterns' => ['store.information*']],
    ];

    $storeMobileNav = array_values(array_filter($storeNav, fn (array $item): bool => $item['mobileTab'] ?? false));
@endphp

@section('sidebar')
    @include('layouts.partials._flowbite_sidebar_nav', ['items' => $storeNav])
@endsection

@section('mobile_nav')
    <x-mobile-tab-bar :items="$storeMobileNav" />
@endsection
