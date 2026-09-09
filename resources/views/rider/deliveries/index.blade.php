@extends('layouts.rider')

@section('title', 'Deliveries')

@section('content')
@if ($assigned->isNotEmpty())
    <section class="mb-8">
        <h2 class="mb-3 font-semibold">Assigned to You</h2>
        <div class="space-y-3">
            @foreach ($assigned as $order)
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-medium">{{ $order->order_number }}</p>
                            <p class="text-sm text-gray-500">{{ $order->store?->store_name }}</p>
                            <p class="mt-1 text-sm">{{ $order->address?->fullAddress() }}</p>
                        </div>
                        <x-order-status-badge :status="$order->status" />
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @if ($order->status === \App\Enums\OrderStatus::RiderAssigned)
                            <form method="POST" action="{{ route('rider.deliveries.pickup', $order) }}">@csrf<button type="submit" class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white">Mark Picked Up</button></form>
                        @elseif ($order->status === \App\Enums\OrderStatus::PickedUp)
                            <form method="POST" action="{{ route('rider.deliveries.deliver', $order) }}">@csrf<button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white">Mark Delivered</button></form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif

@if ($available->isNotEmpty())
    <section class="mb-8">
        <h2 class="mb-3 font-semibold">Available Deliveries</h2>
        <div class="space-y-3">
            @foreach ($available as $order)
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <p class="font-medium">{{ $order->order_number }}</p>
                    <p class="text-sm text-gray-500">{{ $order->store?->store_name }}</p>
                    <p class="text-sm">{{ $order->address?->fullAddress() }}</p>
                    <div class="mt-3 flex gap-2">
                        <form method="POST" action="{{ route('rider.deliveries.accept', $order) }}">@csrf<button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white">Accept</button></form>
                        <form method="POST" action="{{ route('rider.deliveries.decline', $order) }}">@csrf<button type="submit" class="rounded-lg border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">Decline</button></form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif

@if ($completed->isNotEmpty())
    <section>
        <h2 class="mb-3 font-semibold">Recently Completed</h2>
        <div class="divide-y divide-gray-100 rounded-xl border border-gray-200 bg-white">
            @foreach ($completed as $order)
                <div class="flex justify-between px-4 py-3 text-sm">
                    <span>{{ $order->order_number }} · {{ $order->store?->store_name }}</span>
                    <span class="text-gray-500">{{ $order->updated_at->diffForHumans() }}</span>
                </div>
            @endforeach
        </div>
    </section>
@endif

@if ($assigned->isEmpty() && $available->isEmpty() && $completed->isEmpty())
    @include('layouts.partials._empty_state', ['message' => 'No deliveries available'])
@endif
@endsection
