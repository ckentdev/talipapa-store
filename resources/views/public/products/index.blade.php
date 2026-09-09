@extends('layouts.marketplace')

@section('voice_context_attrs')
data-voice-context="products"
@endsection

@section('title', 'Products')

@section('content')
<div class="w-full" data-products-page>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-base font-bold uppercase tracking-widest text-brand-600">Marketplace</p>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Products</h1>
            <p class="mt-1 text-base text-gray-600">Browse fresh items from local stores near you.</p>
        </div>

        <span
            data-products-total-badge
            @class([
                'inline-flex w-fit items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-base text-gray-600 shadow-sm',
                'hidden' => $products->total() === 0,
            ])
        >
            <i class="ri-shopping-basket-2-line text-brand-600" aria-hidden="true"></i>
            <span data-products-total-count>{{ $products->total() }}</span>
            <span data-products-total-label>{{ str('product')->plural($products->total()) }}</span>
        </span>
    </div>

    <x-products.search-filters :categories="$categories" :stores="$stores" class="mb-4" />

    @if (request('nearby') && ! session()->has('latitude'))
        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-base text-amber-800">
            <i class="ri-map-pin-line mt-0.5 shrink-0 text-lg" aria-hidden="true"></i>
            <p>
                Enable location on a product page or add <code class="rounded bg-amber-100 px-1.5 py-0.5 text-sm">?lat=&amp;lng=</code> to save your position for nearby filtering.
            </p>
        </div>
    @endif

    <div class="relative space-y-6 transition-opacity" data-products-results id="products-results">
        <div
            data-products-loading
            class="pointer-events-none absolute inset-0 z-10 hidden items-center justify-center rounded-2xl bg-white/70"
            aria-hidden="true"
        >
            <span class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm">
                <i class="ri-loader-4-line animate-spin text-brand-600" aria-hidden="true"></i>
                Updating results…
            </span>
        </div>

        @include('public.products._results')
    </div>
</div>
@endsection
