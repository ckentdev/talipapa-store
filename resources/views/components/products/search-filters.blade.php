@props([
    'categories',
    'stores',
])

@php
    $selectClass = 'min-w-0 rounded-lg border border-gray-200 bg-white py-2 pl-3 pr-8 text-sm text-gray-700 focus:border-brand-600 focus:ring-brand-600';
    $hasFilters = request()->filled('q')
        || request()->filled('category')
        || request()->filled('store')
        || request()->boolean('nearby');
@endphp

<form
    method="GET"
    action="{{ route('products.index') }}"
    {{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 bg-white p-2 sm:p-3']) }}
    data-products-search
    data-products-filters
    data-header-search
>
    <div class="flex flex-col gap-2 lg:flex-row lg:items-center">
        {{-- Search --}}
        <div class="flex min-w-0 flex-1 items-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50/40">
            <span class="pointer-events-none pl-3 text-gray-400" aria-hidden="true">
                <i class="ri-search-line text-base"></i>
            </span>

            <input
                type="search"
                name="q"
                value="{{ request()->query('q') }}"
                placeholder="Search products..."
                class="header-search-input min-w-0 flex-1 border-0 bg-transparent px-2 py-2.5 text-sm text-gray-700 placeholder:text-gray-400 focus:ring-0"
                autocomplete="off"
                aria-controls="products-results"
            >

            <span
                data-products-search-loading
                class="pointer-events-none hidden shrink-0 pr-1 text-brand-600"
                aria-hidden="true"
            >
                <i class="ri-loader-4-line animate-spin text-base"></i>
            </span>

            <button
                type="button"
                data-voice-search
                class="flex h-9 w-9 shrink-0 items-center justify-center border-l border-gray-200 text-forest-600/70 transition hover:bg-white hover:text-brand-600"
                aria-label="Search by voice"
                aria-pressed="false"
                title="Speak to search"
            >
                <i class="ri-mic-line text-base" aria-hidden="true"></i>
            </button>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-2 lg:shrink-0">
            <label class="sr-only" for="products-category">Category</label>
            <select id="products-category" name="category" class="{{ $selectClass }} w-full sm:w-auto sm:min-w-[9.5rem]">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request()->query('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>

            <label class="sr-only" for="products-store">Store</label>
            <select id="products-store" name="store" class="{{ $selectClass }} w-full sm:w-auto sm:min-w-[9.5rem]">
                <option value="">All stores</option>
                @foreach ($stores as $store)
                    <option value="{{ $store->id }}" @selected(request()->query('store') == $store->id)>{{ $store->store_name }}</option>
                @endforeach
            </select>

            <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-2 text-sm text-gray-700">
                <input
                    type="checkbox"
                    name="nearby"
                    value="1"
                    @checked(request()->boolean('nearby'))
                    class="rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                >
                Nearby
            </label>

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-700 sm:min-w-[5.5rem]"
            >
                <i class="ri-filter-3-line text-base" aria-hidden="true"></i>
                Apply
            </button>

            @if ($hasFilters)
                <a
                    href="{{ route('products.index') }}"
                    class="inline-flex items-center justify-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50"
                    title="Clear all filters"
                >
                    <i class="ri-close-line text-base" aria-hidden="true"></i>
                    Clear
                </a>
            @endif
        </div>
    </div>

    <p data-voice-search-status class="sr-only" role="status" aria-live="polite"></p>
</form>
