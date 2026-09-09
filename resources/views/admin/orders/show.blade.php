@extends('layouts.admin')

@section('title', $order->order_number)

@section('content')
@php
    $paymentDetail = collect(preg_split('/\R/', (string) $order->notes))
        ->first(fn ($line) => is_string($line) && (str_starts_with($line, 'e-Wallet:') || str_starts_with($line, 'Card:')));
    $inputClass = 'block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-base focus:border-brand-600 focus:ring-brand-600';
@endphp

<div class="w-full">
    <a
        href="{{ route('admin.orders') }}"
        class="mb-4 inline-flex items-center gap-1 text-base font-medium text-gray-500 hover:text-brand-600"
    >
        <i class="ri-arrow-left-s-line text-base" aria-hidden="true"></i>
        Back to customer orders
    </a>

    <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-6 py-5 sm:px-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="font-mono text-base font-bold uppercase tracking-wide text-brand-600">Customer order</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900 sm:text-3xl">{{ $order->order_number }}</h1>
                    <p class="mt-2 text-base text-gray-500">{{ $order->created_at->format('M d, Y · h:i A') }}</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <x-order-status-badge :status="$order->status" class="px-4 py-1.5 text-sm" />
                    <p class="text-2xl font-bold text-brand-600">₱{{ number_format($order->total, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid items-start gap-6 lg:grid-cols-3 lg:gap-8">
        <div class="space-y-6 lg:col-span-2">
            <x-customer.orders.timeline :order="$order" :timeline="$timeline" />

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/60 to-white px-6 py-5 sm:px-8">
                    <div class="flex items-start gap-4">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                            <i class="ri-shopping-basket-2-line text-xl" aria-hidden="true"></i>
                        </span>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Order Items</h2>
                            <p class="mt-0.5 text-base text-gray-600">{{ $order->items->sum('quantity') }} {{ str('item')->plural($order->items->sum('quantity')) }}</p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach ($order->items as $item)
                        <div class="flex gap-4 px-6 py-4 sm:px-8">
                            @if ($item->product)
                                <x-product-image
                                    :product="$item->product"
                                    class="h-16 w-16 shrink-0 rounded-lg bg-gray-100 object-cover ring-1 ring-gray-100 sm:h-20 sm:w-20"
                                />
                            @else
                                <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-400 ring-1 ring-gray-100 sm:h-20 sm:w-20">
                                    <i class="ri-image-line text-xl" aria-hidden="true"></i>
                                </span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="text-base font-semibold text-gray-900">{{ $item->product?->name ?? 'Product' }}</p>
                                <p class="text-base text-gray-500">Qty {{ $item->quantity }} · ₱{{ number_format($item->unit_price, 2) }} each</p>
                            </div>
                            <p class="shrink-0 text-base font-semibold text-gray-900">
                                ₱{{ number_format($item->unit_price * $item->quantity, 2) }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="space-y-2 border-t border-gray-100 bg-gray-50/40 px-6 py-4 text-base sm:px-8">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-medium text-gray-900">₱{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Delivery fee</span>
                        <span class="font-medium text-gray-900">₱{{ number_format($order->delivery_fee, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 text-lg font-bold text-gray-900">
                        <span>Total</span>
                        <span class="text-brand-600">₱{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            @if ($order->rejection_reason)
                <div class="rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-base text-red-700">
                    <p class="font-semibold">Rejection reason</p>
                    <p class="mt-1">{{ $order->rejection_reason }}</p>
                </div>
            @endif
        </div>

        <aside class="space-y-6 lg:sticky lg:top-6">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                            <i class="ri-team-line text-lg" aria-hidden="true"></i>
                        </span>
                        <h2 class="text-lg font-bold text-gray-900">Parties</h2>
                    </div>
                </div>
                <dl class="space-y-4 px-5 py-4 text-base sm:px-6">
                    <div>
                        <dt class="font-bold uppercase tracking-wider text-gray-400">Customer</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $order->customer?->name ?? '—' }}</dd>
                        @if ($order->customer?->phone)
                            <dd class="text-gray-600">{{ $order->customer->phone }}</dd>
                        @endif
                        @if ($order->customer?->email)
                            <dd class="text-gray-600">{{ $order->customer->email }}</dd>
                        @endif
                    </div>
                    <div class="border-t border-gray-100 pt-4">
                        <dt class="font-bold uppercase tracking-wider text-gray-400">Store</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $order->store?->store_name ?? '—' }}</dd>
                    </div>
                    <div class="border-t border-gray-100 pt-4">
                        <dt class="font-bold uppercase tracking-wider text-gray-400">Rider</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $order->rider?->name ?? 'Unassigned' }}</dd>
                        @if ($order->rider?->phone)
                            <dd class="text-gray-600">{{ $order->rider->phone }}</dd>
                        @endif
                    </div>
                </dl>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                            <i class="ri-map-pin-2-line text-lg" aria-hidden="true"></i>
                        </span>
                        <h2 class="text-lg font-bold text-gray-900">Delivery</h2>
                    </div>
                </div>
                <div class="px-5 py-4 text-base sm:px-6">
                    <p class="leading-relaxed text-gray-700">{{ $order->address?->fullAddress() ?? '—' }}</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                            <i class="ri-bank-card-line text-lg" aria-hidden="true"></i>
                        </span>
                        <h2 class="text-lg font-bold text-gray-900">Payment</h2>
                    </div>
                </div>
                <div class="px-5 py-4 text-base sm:px-6">
                    <p class="font-medium text-gray-900">{{ $order->payment_method->label() }}</p>
                    @if ($paymentDetail)
                        <p class="mt-1 text-gray-600">{{ str($paymentDetail)->after(': ')->toString() }}</p>
                    @endif
                </div>
            </div>

            @if ($order->status === \App\Enums\OrderStatus::ReadyForPickup)
                <div class="overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm">
                    <div class="border-b border-brand-100 bg-gradient-to-r from-brand-50/80 to-white px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                                <i class="ri-e-bike-line text-lg" aria-hidden="true"></i>
                            </span>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Assign Rider</h2>
                                <p class="text-base text-gray-500">Order is ready for pickup.</p>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.orders.assign-rider', $order) }}" class="space-y-4 px-5 py-4 sm:px-6">
                        @csrf
                        <div>
                            <label for="rider_id" class="mb-1.5 block text-base font-medium text-gray-700">Available rider</label>
                            <select id="rider_id" name="rider_id" required class="{{ $inputClass }}">
                                <option value="">Select rider...</option>
                                @foreach ($availableRiders as $rider)
                                    <option value="{{ $rider->id }}" @selected(old('rider_id') == $rider->id)>{{ $rider->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="rider_id" class="mt-1" />
                        </div>
                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2.5 text-base font-semibold text-white hover:bg-brand-700"
                        >
                            <i class="ri-user-add-line" aria-hidden="true"></i>
                            Assign rider
                        </button>
                    </form>
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection
