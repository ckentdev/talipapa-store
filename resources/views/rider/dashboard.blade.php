@extends('layouts.rider')

@section('title', 'Dashboard')

@section('content')
@if ($profile && $profile->status !== \App\Enums\ApprovalStatus::Approved)
    <div class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
        Your rider account is <strong>{{ $profile->status->value }}</strong>.
        @if ($profile->rejection_reason) Reason: {{ $profile->rejection_reason }} @endif
    </div>
@endif

<div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-gray-200 bg-white p-4">
    <div>
        <p class="text-sm text-gray-500">Availability</p>
        <p class="font-semibold capitalize">{{ $profile?->availability?->value ?? 'unavailable' }}</p>
    </div>
    @if ($profile?->status === \App\Enums\ApprovalStatus::Approved)
        <form method="POST" action="{{ route('rider.availability.toggle') }}">
            @csrf
            <button type="submit" @class([
                'rounded-lg px-4 py-2 text-sm font-medium text-white',
                'bg-green-600 hover:bg-green-700' => $profile->availability !== \App\Enums\RiderAvailability::Available,
                'bg-gray-600 hover:bg-gray-700' => $profile->availability === \App\Enums\RiderAvailability::Available,
            ])>
                {{ $profile->availability === \App\Enums\RiderAvailability::Available ? 'Go Offline' : 'Go Online' }}
            </button>
        </form>
    @endif
</div>

<div class="mb-6 grid grid-cols-2 gap-4">
    <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-sm text-gray-500">Today's Deliveries</p><p class="text-2xl font-bold">{{ $stats['today_deliveries'] }}</p></div>
    <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-sm text-gray-500">Available Orders</p><p class="text-2xl font-bold text-brand-600">{{ $stats['pending_offers'] }}</p></div>
</div>

@if ($activeDelivery)
    <div class="rounded-xl border-2 border-brand-200 bg-brand-50 p-4">
        <h2 class="mb-2 font-semibold text-brand-800">Active Delivery</h2>
        <p class="text-sm">{{ $activeDelivery->order_number }} · {{ $activeDelivery->store?->store_name }}</p>
        <p class="text-sm text-gray-600">{{ $activeDelivery->address?->fullAddress() }}</p>
        <a href="{{ route('rider.deliveries') }}" class="mt-3 inline-block text-sm font-medium text-brand-700 hover:underline">View delivery →</a>
    </div>
@else
    <a href="{{ route('rider.deliveries') }}" class="block rounded-xl border border-gray-200 bg-white p-6 text-center hover:shadow-sm">
        <p class="font-medium">No active delivery</p>
        <p class="mt-1 text-sm text-brand-600">Browse available deliveries →</p>
    </a>
@endif
@endsection
