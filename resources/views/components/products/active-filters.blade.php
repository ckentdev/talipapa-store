@props([
    'categories',
    'stores',
])

@php
    $selectedCategory = $categories->firstWhere('id', request()->integer('category'));
    $selectedStore = $stores->firstWhere('id', request()->integer('store'));
    $filters = collect([
        request()->filled('q') ? [
            'label' => 'Search: "'.request()->string('q').'"',
            'url' => route('products.index', request()->except('q')),
        ] : null,
        $selectedCategory ? [
            'label' => 'Category: '.$selectedCategory->name,
            'url' => route('products.index', request()->except('category')),
        ] : null,
        $selectedStore ? [
            'label' => 'Store: '.$selectedStore->store_name,
            'url' => route('products.index', request()->except('store')),
        ] : null,
        request()->boolean('nearby') ? [
            'label' => 'Nearby only',
            'url' => route('products.index', request()->except('nearby')),
        ] : null,
    ])->filter();
@endphp

@if ($filters->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }}>
        <span class="text-base font-medium text-gray-500">Active:</span>

        @foreach ($filters as $filter)
            <a
                href="{{ $filter['url'] }}"
                class="inline-flex items-center gap-1.5 rounded-full border border-brand-200 bg-brand-50 px-3 py-1.5 text-sm font-medium text-brand-700 transition hover:bg-brand-100"
            >
                {{ $filter['label'] }}
                <i class="ri-close-line text-base" aria-hidden="true"></i>
            </a>
        @endforeach
    </div>
@endif
