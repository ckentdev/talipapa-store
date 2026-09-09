@extends('layouts.rider')

@section('title', 'Earnings')

@section('content')
<div class="mb-6 grid grid-cols-3 gap-4">
    <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-xs text-gray-500">Total</p><p class="text-xl font-bold">{{ $summary['total_deliveries'] }}</p></div>
    <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-xs text-gray-500">Today</p><p class="text-xl font-bold">{{ $summary['today_deliveries'] }}</p></div>
    <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-xs text-gray-500">Fees Earned</p><p class="text-xl font-bold text-brand-600">₱{{ number_format($summary['total_earnings'], 2) }}</p></div>
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="divide-y divide-gray-100">
        @forelse ($orders as $order)
            <div class="flex justify-between px-4 py-3 text-sm">
                <div>
                    <p class="font-medium">{{ $order->order_number }}</p>
                    <p class="text-gray-500">{{ $order->store?->store_name }}</p>
                </div>
                <div class="text-right">
                    <p class="font-medium text-brand-600">₱{{ number_format($order->delivery_fee, 2) }}</p>
                    <p class="text-xs text-gray-400">{{ $order->updated_at->format('M d') }}</p>
                </div>
            </div>
        @empty
            @include('layouts.partials._empty_state', ['message' => 'No completed deliveries yet'])
        @endforelse
    </div>
</div>
<div class="mt-6">{{ $orders->links() }}</div>
@endsection
