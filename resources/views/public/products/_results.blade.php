@php
    $hasFilters = request()->filled('q')
        || request()->filled('category')
        || request()->filled('store')
        || request()->boolean('nearby');
@endphp

<div
    data-products-results-meta
    data-total="{{ $products->total() }}"
    data-from="{{ $products->firstItem() ?? 0 }}"
    data-to="{{ $products->lastItem() ?? 0 }}"
    data-page="{{ $products->currentPage() }}"
    data-last-page="{{ $products->lastPage() }}"
    class="hidden"
    aria-hidden="true"
></div>

<x-products.active-filters :categories="$categories" :stores="$stores" />

@if ($products->isEmpty())
    <div class="overflow-hidden rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center shadow-sm">
        <span class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full bg-brand-50 text-brand-600">
            <i class="ri-shopping-basket-2-line text-3xl" aria-hidden="true"></i>
        </span>
        <h2 class="mt-5 text-xl font-bold text-gray-900">No products found</h2>
        <p class="mx-auto mt-2 max-w-md text-base text-gray-500">
            @if ($hasFilters)
                Nothing matches your current filters. Try adjusting your search or clearing filters.
            @else
                Products from approved stores will appear here once they are listed.
            @endif
        </p>
        @if ($hasFilters)
            <a
                href="{{ route('products.index') }}"
                class="mt-6 inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-6 py-2.5 text-base font-semibold text-white hover:bg-brand-700"
            >
                <i class="ri-filter-off-line" aria-hidden="true"></i>
                Clear filters
            </a>
        @endif
    </div>
@else
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-base text-gray-600" data-products-results-summary>
            Showing
            <span class="font-semibold text-gray-900">{{ $products->firstItem() }}–{{ $products->lastItem() }}</span>
            of
            <span class="font-semibold text-gray-900">{{ $products->total() }}</span>
        </p>

        @if ($products->hasPages())
            <p class="text-base text-gray-500" data-products-results-page>
                Page {{ $products->currentPage() }} of {{ $products->lastPage() }}
            </p>
        @endif
    </div>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
        @foreach ($products as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>

    @if ($products->hasPages())
        <div class="mt-4" data-products-pagination>
            {{ $products->links() }}
        </div>
    @endif
@endif
