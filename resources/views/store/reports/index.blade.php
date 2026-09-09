@extends('layouts.store')

@section('title', 'Sales Report')

@section('content')
<form method="GET" class="mb-6 flex flex-wrap gap-3 rounded-xl border border-gray-200 bg-white p-4">
    <div>
        <label class="mb-1 block text-xs font-medium text-gray-500">From</label>
        <input type="date" name="from" value="{{ $from }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-xs font-medium text-gray-500">To</label>
        <input type="date" name="to" value="{{ $to }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
    </div>
    <div class="flex items-end">
        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Filter</button>
    </div>
</form>

<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-sm text-gray-500">Orders</p><p class="text-2xl font-bold">{{ $summary['total_orders'] }}</p></div>
    <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-sm text-gray-500">Total Sales</p><p class="text-2xl font-bold text-brand-600">₱{{ number_format($summary['total_sales'], 2) }}</p></div>
    <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-sm text-gray-500">Subtotal</p><p class="text-2xl font-bold">₱{{ number_format($summary['total_subtotal'], 2) }}</p></div>
    <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-sm text-gray-500">Delivery Fees</p><p class="text-2xl font-bold">₱{{ number_format($summary['total_delivery_fees'], 2) }}</p></div>
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Order</th><th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-right">Total</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($orders as $order)
                <tr><td class="px-4 py-3">{{ $order->order_number }}</td><td class="px-4 py-3">{{ $order->updated_at->format('M d, Y') }}</td><td class="px-4 py-3 text-right">₱{{ number_format($order->total, 2) }}</td></tr>
            @empty
                <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">No sales in this period</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
