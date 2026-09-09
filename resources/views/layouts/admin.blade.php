@extends('layouts.marketplace')

@php
    use App\Enums\OrderStatus;
    use App\Models\Order;

    $pendingOrdersCount = Order::query()
        ->where('status', OrderStatus::Pending)
        ->count();

    $adminNav = [
        ['label' => 'Dashboard', 'mobileLabel' => 'Home', 'route' => 'admin.dashboard', 'icon' => 'dashboard', 'patterns' => ['admin.dashboard']],
        ['label' => 'Users', 'mobileLabel' => 'Users', 'route' => 'admin.users', 'icon' => 'users', 'patterns' => ['admin.users*']],
        ['label' => 'Approvals', 'mobileLabel' => 'Approvals', 'route' => 'admin.approvals', 'icon' => 'approvals', 'patterns' => ['admin.approvals*']],
        [
            'label' => 'Customer Orders',
            'mobileLabel' => 'Orders',
            'route' => 'admin.orders',
            'icon' => 'orders',
            'patterns' => ['admin.orders*'],
            'badge' => $pendingOrdersCount > 0 ? $pendingOrdersCount : null,
            'badgeType' => 'pending',
        ],
        ['label' => 'Voice of Customer', 'mobileLabel' => 'VoC', 'route' => 'admin.voc.index', 'icon' => 'voc', 'patterns' => ['admin.voc*']],
        ['label' => 'Settings', 'mobileLabel' => 'Settings', 'route' => 'admin.settings', 'icon' => 'settings', 'patterns' => ['admin.settings*']],
    ];
@endphp

@section('sidebar')
    @include('layouts.partials._flowbite_sidebar_nav', ['items' => $adminNav])
@endsection

@section('mobile_nav')
    <x-mobile-tab-bar :items="$adminNav" />
@endsection
