@extends('layouts.admin')

@section('title', 'Approvals')

@section('content')
<section class="mb-10">
    <h2 class="mb-4 text-lg font-semibold">Pending Stores ({{ $pendingStores->count() }})</h2>
    <div class="space-y-4">
        @forelse ($pendingStores as $store)
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold">{{ $store->store_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $store->user?->name }} · {{ $store->user?->email }}</p>
                        <p class="mt-1 text-sm">{{ $store->description }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.approvals.stores.approve', $store) }}">@csrf<button type="submit" class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white">Approve</button></form>
                        <form method="POST" action="{{ route('admin.approvals.stores.suspend', $store) }}">@csrf<button type="submit" class="rounded-lg bg-gray-600 px-3 py-1.5 text-sm font-medium text-white">Suspend</button></form>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.approvals.stores.reject', $store) }}" class="mt-3 flex gap-2">
                    @csrf
                    <input type="text" name="rejection_reason" placeholder="Rejection reason" required class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white">Reject</button>
                </form>
            </div>
        @empty
            <p class="text-sm text-gray-500">No pending store applications.</p>
        @endforelse
    </div>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold">Pending Riders ({{ $pendingRiders->count() }})</h2>
    <div class="space-y-4">
        @forelse ($pendingRiders as $rider)
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold">{{ $rider->user?->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $rider->user?->email }} · {{ $rider->vehicle_type }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.approvals.riders.approve', $rider) }}">@csrf<button type="submit" class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white">Approve</button></form>
                        <form method="POST" action="{{ route('admin.approvals.riders.suspend', $rider) }}">@csrf<button type="submit" class="rounded-lg bg-gray-600 px-3 py-1.5 text-sm font-medium text-white">Suspend</button></form>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.approvals.riders.reject', $rider) }}" class="mt-3 flex gap-2">
                    @csrf
                    <input type="text" name="rejection_reason" placeholder="Rejection reason" required class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white">Reject</button>
                </form>
            </div>
        @empty
            <p class="text-sm text-gray-500">No pending rider applications.</p>
        @endforelse
    </div>
</section>
@endsection
