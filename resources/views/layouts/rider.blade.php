@extends('layouts.marketplace')

@php
    $riderNav = [
        ['label' => 'Dashboard', 'mobileLabel' => 'Home', 'route' => 'rider.dashboard', 'icon' => 'dashboard', 'patterns' => ['rider.dashboard']],
        ['label' => 'Deliveries', 'mobileLabel' => 'Deliveries', 'route' => 'rider.deliveries', 'icon' => 'deliveries', 'patterns' => ['rider.deliveries*']],
        ['label' => 'Earnings', 'mobileLabel' => 'Earnings', 'route' => 'rider.earnings', 'icon' => 'earnings', 'patterns' => ['rider.earnings*']],
        ['label' => 'Account', 'mobileLabel' => 'Account', 'route' => 'rider.account', 'icon' => 'account', 'patterns' => ['rider.account*']],
    ];
@endphp

@section('sidebar')
    @include('layouts.partials._flowbite_sidebar_nav', ['items' => $riderNav])
@endsection

@section('mobile_nav')
    <x-mobile-tab-bar :items="$riderNav" />
@endsection
