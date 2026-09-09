@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    @foreach ([
        ['Users', $kpis['total_users'], ''],
        ['Stores', $kpis['total_stores'], ''],
        ['Pending Stores', $kpis['pending_stores'], 'text-yellow-600'],
        ['Pending Riders', $kpis['pending_riders'], 'text-yellow-600'],
        ['Active Orders', $kpis['active_orders'], 'text-blue-600'],
        ['Today Orders', $kpis['today_orders'], ''],
        ['Today Revenue', '₱'.number_format($kpis['today_revenue'], 2), 'text-brand-600'],
    ] as [$label, $value, $color])
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">{{ $label }}</p>
            <p class="mt-1 text-2xl font-bold {{ $color }}">{{ $value }}</p>
        </div>
    @endforeach
</div>

<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <a href="{{ route('admin.approvals') }}" class="rounded-xl border border-gray-200 bg-white p-4 hover:shadow-sm"><p class="font-medium">Approvals</p><p class="text-sm text-gray-500">Review pending applications</p></a>
    <a href="{{ route('admin.categories.index') }}" class="rounded-xl border border-gray-200 bg-white p-4 hover:shadow-sm"><p class="font-medium">Categories</p><p class="text-sm text-gray-500">Manage product categories</p></a>
    <a href="{{ route('admin.voc.index') }}" class="rounded-xl border border-gray-200 bg-white p-4 hover:shadow-sm"><p class="font-medium">Voice of Customer</p><p class="text-sm text-gray-500">Demand, gaps, and pricing signals</p></a>
    <a href="{{ route('admin.map.index') }}" class="rounded-xl border border-gray-200 bg-white p-4 hover:shadow-sm"><p class="font-medium">Map</p><p class="text-sm text-gray-500">View geotagged locations</p></a>
    <a href="{{ route('admin.psgc.index') }}" class="rounded-xl border border-gray-200 bg-white p-4 hover:shadow-sm"><p class="font-medium">PSGC</p><p class="text-sm text-gray-500">Browse address data</p></a>
</div>

<div class="rounded-xl border border-gray-200 bg-white">
    <div class="border-b border-gray-200 px-4 py-3"><h2 class="font-semibold">Recent Orders</h2></div>
    <div class="divide-y divide-gray-100">
        @foreach ($recentOrders as $order)
            <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
                <div><p class="font-medium">{{ $order->order_number }}</p><p class="text-sm text-gray-500">{{ $order->customer?->name }} · {{ $order->store?->store_name }}</p></div>
                <x-order-status-badge :status="$order->status" />
            </a>
        @endforeach
    </div>
</div>

<div class="mt-6 rounded-xl border border-gray-200 bg-white p-4">
    <h3 class="mb-3 font-semibold">Export Orders (CSV)</h3>
    <form method="GET" action="{{ route('admin.reports.export') }}" class="flex flex-wrap gap-3">
        <input type="date" name="from" value="{{ now()->startOfMonth()->toDateString() }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <input type="date" name="to" value="{{ now()->toDateString() }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white">Download CSV</button>
    </form>
</div>
@endsection
