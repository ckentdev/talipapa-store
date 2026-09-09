@extends('layouts.admin')

@section('title', 'Customer Orders')

@section('content')
@php
    $inputClass = 'block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-base focus:border-brand-600 focus:ring-brand-600';
    $activeStatus = request('status');
@endphp

<div class="w-full">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-base font-bold uppercase tracking-widest text-brand-600">Admin</p>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Customer Orders</h1>
            <p class="mt-1 text-base text-gray-600">Monitor and manage orders placed across all stores.</p>
        </div>

        @if ($orders->total() > 0)
            <span class="inline-flex w-fit items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-base text-gray-600 shadow-sm">
                <i class="ri-shopping-bag-3-line text-brand-600" aria-hidden="true"></i>
                {{ $orders->total() }} {{ str('order')->plural($orders->total()) }}
            </span>
        @endif
    </div>

    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/60 to-white px-5 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                    <i class="ri-filter-3-line text-lg" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Filter orders</h2>
                    <p class="text-base text-gray-500">Narrow results by order status.</p>
                </div>
            </div>
        </div>

        <form method="GET" class="flex flex-wrap items-end gap-4 px-5 py-4 sm:px-6">
            <div class="min-w-[14rem] flex-1">
                <label for="status" class="mb-1.5 block text-base font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="{{ $inputClass }}">
                    <option value="">All statuses</option>
                    @foreach (\App\Enums\OrderStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected($activeStatus === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <button
                type="submit"
                class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2.5 text-base font-semibold text-white hover:bg-brand-700"
            >
                <i class="ri-search-line" aria-hidden="true"></i>
                Apply filter
            </button>
            @if ($activeStatus)
                <a
                    href="{{ route('admin.orders') }}"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-base font-medium text-gray-700 hover:bg-gray-50"
                >
                    Clear
                </a>
            @endif
        </form>
    </div>

    @if ($orders->isEmpty())
        <div class="overflow-hidden rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center shadow-sm">
            <span class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                <i class="ri-shopping-bag-3-line text-3xl" aria-hidden="true"></i>
            </span>
            <h2 class="mt-5 text-xl font-bold text-gray-900">No orders found</h2>
            <p class="mx-auto mt-2 max-w-md text-base text-gray-500">
                @if ($activeStatus)
                    No orders match the selected status. Try clearing the filter.
                @else
                    Customer orders will appear here once shoppers start placing orders.
                @endif
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <x-admin.orders.order-card :order="$order" />
            @endforeach
        </div>

        @if ($orders->hasPages())
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
