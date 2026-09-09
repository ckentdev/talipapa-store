@extends('layouts.marketplace')

@php
    $customerNav = [
        ['label' => 'Home', 'mobileLabel' => 'Home', 'route' => 'landing', 'icon' => 'home', 'patterns' => ['landing']],
        ['label' => 'Shop', 'mobileLabel' => 'Shop', 'route' => 'products.index', 'icon' => 'products', 'patterns' => ['products.*']],
        ['label' => 'Cart', 'mobileLabel' => 'Cart', 'route' => 'cart.index', 'icon' => 'cart', 'patterns' => ['cart.*', 'customer.checkout*']],
        ['label' => 'Orders', 'mobileLabel' => 'Orders', 'route' => 'customer.orders.index', 'icon' => 'orders', 'patterns' => ['customer.orders*']],
        ['label' => 'Account', 'mobileLabel' => 'Account', 'route' => 'customer.account', 'icon' => 'account', 'patterns' => ['customer.account*', 'customer.addresses*']],
    ];
@endphp

@section('sidebar')
    @include('layouts.partials._flowbite_sidebar_nav', ['items' => $customerNav])
@endsection

@section('mobile_nav')
    <x-mobile-tab-bar :items="$customerNav" />
@endsection
