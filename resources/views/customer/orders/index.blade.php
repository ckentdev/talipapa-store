@extends('layouts.marketplace')

@section('title', 'My Orders')

@section('content')
<div class="mx-auto max-w-6xl">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-base font-bold uppercase tracking-widest text-brand-600">Account</p>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">My Orders</h1>
            <p class="mt-1 text-base text-gray-600">Track deliveries and review your purchase history.</p>
        </div>

        @if ($orders->total() > 0)
            <span class="inline-flex w-fit items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-base text-gray-600 shadow-sm">
                <i class="ri-shopping-bag-3-line text-brand-600" aria-hidden="true"></i>
                {{ $orders->total() }} {{ str('order')->plural($orders->total()) }}
            </span>
        @endif
    </div>

    @if ($orders->isEmpty())
        <div class="overflow-hidden rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center shadow-sm">
            <span class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                <i class="ri-shopping-bag-3-line text-3xl" aria-hidden="true"></i>
            </span>
            <h2 class="mt-5 text-xl font-bold text-gray-900">No orders yet</h2>
            <p class="mx-auto mt-2 max-w-md text-base text-gray-500">
                When you place an order, it will show up here so you can track its progress.
            </p>
            <a
                href="{{ route('products.index') }}"
                class="mt-6 inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-6 py-2.5 text-base font-semibold text-white hover:bg-brand-700"
            >
                <i class="ri-store-2-line" aria-hidden="true"></i>
                Start shopping
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <x-customer.orders.order-card :order="$order" />
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
