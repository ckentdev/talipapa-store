@php
    use App\Models\Category;
    use App\Models\StoreProfile;

    $searchCategories = Category::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    $searchStores = StoreProfile::query()->approved()->orderBy('store_name')->get(['id', 'store_name']);
    $selectedStoreId = request()->query('store');
    $selectedCategoryId = request()->query('category');
@endphp

<form action="{{ route('products.index') }}" method="GET" class="header-search-form flex w-full min-w-0 flex-1 items-center" data-header-search>
    <div class="relative flex w-full items-center overflow-visible rounded-full border border-avocado-200/70 bg-white shadow-sm focus-within:border-brand-400 focus-within:ring-2 focus-within:ring-brand-100">
        <span class="pointer-events-none pl-4 text-gray-400" aria-hidden="true">
            <i class="ri-search-line text-base"></i>
        </span>

        <input
            type="search"
            name="q"
            value="{{ request()->query('q') }}"
            placeholder="Search products, stores..."
            class="header-search-input min-w-0 flex-1 border-0 bg-transparent px-3 py-2.5 text-sm text-gray-700 placeholder:text-gray-400 focus:ring-0"
            autocomplete="off"
        >

        {{-- Filter toggle --}}
        <div class="relative shrink-0 border-l border-avocado-200/70">
        <button
            type="button"
            data-search-filter-toggle
            class="flex h-10 items-center gap-1.5 px-3.5 text-sm font-medium text-forest-600/70 hover:text-brand-600 transition-colors"
                aria-expanded="false"
                aria-controls="header-search-filters"
            >
                <i class="ri-equalizer-line text-base" aria-hidden="true"></i>
                <span class="hidden sm:inline">Filter</span>
            </button>

            <div
                id="header-search-filters"
                data-search-filter-panel
                class="absolute right-0 top-full z-50 mt-2 hidden w-72 rounded-xl border border-gray-200 bg-white p-4 shadow-xl"
            >
                <p class="mb-3 text-xs font-bold uppercase tracking-wide text-gray-500">Search filters</p>

                <label class="mb-3 block">
                    <span class="mb-1 block text-xs font-medium text-gray-600">Category</span>
                    <select name="category" class="w-full rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">All categories</option>
                        @foreach ($searchCategories as $category)
                            <option value="{{ $category->id }}" @selected($selectedCategoryId == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="mb-3 block">
                    <span class="mb-1 block text-xs font-medium text-gray-600">Store</span>
                    <select name="store" class="w-full rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">All stores</option>
                        @foreach ($searchStores as $store)
                            <option value="{{ $store->id }}" @selected($selectedStoreId == $store->id)>{{ $store->store_name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="flex cursor-pointer items-center gap-2">
                    <input type="checkbox" name="nearby" value="1" @checked(request()->boolean('nearby')) class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm text-gray-700">Nearby stores only</span>
                </label>

                <div class="mt-4 flex gap-2">
                    <button type="submit" class="flex-1 rounded-lg bg-brand-600 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-700">Apply</button>
                    <button type="button" data-search-filter-close class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50">Close</button>
                </div>
            </div>
        </div>

        {{-- Voice search --}}
        <button
            type="button"
            data-voice-search
            class="flex h-10 w-11 shrink-0 items-center justify-center border-l border-avocado-200/70 text-forest-600/70 hover:bg-avocado-50 hover:text-brand-600 transition-colors"
            aria-label="Search by voice"
            aria-pressed="false"
            title="Speak to search"
        >
            <i class="ri-mic-line text-base" aria-hidden="true"></i>
        </button>

        <button type="submit" class="m-1.5 ml-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-forest-600 text-white hover:bg-forest-700 transition-colors" aria-label="Search">
            <i class="ri-arrow-right-line text-base" aria-hidden="true"></i>
        </button>
    </div>

    <p data-voice-search-status class="sr-only" role="status" aria-live="polite"></p>
</form>
