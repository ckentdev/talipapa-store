@extends('layouts.marketplace')

@section('voice_context_attrs')
data-voice-context="cart"
@endsection

@section('title', 'Cart')

@section('content')
@php
    $itemsByStore = $cart->items->groupBy(fn ($item) => $item->product->store_profile_id ?? 0);
@endphp

<div class="mx-auto max-w-6xl">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a
                href="{{ route('products.index') }}"
                class="mb-2 inline-flex items-center gap-1 text-base font-medium text-gray-500 hover:text-brand-600"
            >
                <i class="ri-arrow-left-s-line text-base" aria-hidden="true"></i>
                Continue shopping
            </a>
            <p class="text-base font-bold uppercase tracking-widest text-brand-600">Shopping</p>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Your Cart</h1>
            <p class="mt-1 text-base text-gray-600">Review items before checkout.</p>
        </div>

        @if ($cart->items->isNotEmpty())
            <span class="inline-flex w-fit items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-base text-gray-600 shadow-sm">
                <i class="ri-shopping-cart-2-line text-brand-600" aria-hidden="true"></i>
                {{ $itemCount }} {{ str('item')->plural($itemCount) }}
            </span>
        @endif
    </div>

    @if ($cart->items->isEmpty())
        <div class="overflow-hidden rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center shadow-sm">
            <span class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                <i class="ri-shopping-cart-2-line text-3xl" aria-hidden="true"></i>
            </span>
            <h2 class="mt-5 text-xl font-bold text-gray-900">Your cart is empty</h2>
            <p class="mx-auto mt-2 max-w-md text-base text-gray-500">
                Browse local stores and add fresh products to get started.
            </p>
            <a
                href="{{ route('products.index') }}"
                class="mt-6 inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-6 py-2.5 text-base font-semibold text-white hover:bg-brand-700"
            >
                <i class="ri-store-2-line" aria-hidden="true"></i>
                Browse products
            </a>
        </div>
    @else
        <div class="grid items-start gap-6 lg:grid-cols-3 lg:gap-8">
            <div class="space-y-6 lg:col-span-2">
                @foreach ($itemsByStore as $storeItems)
                    @php
                        $store = $storeItems->first()->product->store;
                        $storeSubtotal = $storeItems->sum(fn ($item) => $item->total());
                    @endphp
                    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/60 to-white px-5 py-4 sm:px-6">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                                        <i class="ri-store-2-line text-lg" aria-hidden="true"></i>
                                    </span>
                                    <div>
                                        <h2 class="text-lg font-bold text-gray-900">{{ $store?->store_name ?? 'Store' }}</h2>
                                        <p class="text-base text-gray-500">
                                            {{ $storeItems->sum('quantity') }} {{ str('item')->plural($storeItems->sum('quantity')) }}
                                        </p>
                                    </div>
                                </div>
                                <p class="text-base font-semibold text-brand-600">₱{{ number_format($storeSubtotal, 2) }}</p>
                            </div>
                        </div>

                        @foreach ($storeItems as $item)
                            <x-cart.item :item="$item" />
                        @endforeach
                    </section>
                @endforeach
            </div>

            <x-cart.summary :cart="$cart" :item-count="$itemCount" />
        </div>
    @endif
</div>
@endsection
