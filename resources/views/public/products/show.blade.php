@extends('layouts.marketplace')

@section('title', $product->name)

@section('content')
@php
    $lowStock = $product->stock <= 5;
@endphp

<div class="mx-auto max-w-7xl">
    <a
        href="{{ route('products.index') }}"
        class="mb-4 inline-flex items-center gap-1 text-base font-medium text-gray-500 hover:text-brand-600"
    >
        <i class="ri-arrow-left-s-line text-base" aria-hidden="true"></i>
        Back to products
    </a>

    <div class="grid items-start gap-6 lg:grid-cols-3 lg:gap-8">
        <div class="space-y-6 lg:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <x-product-image
                    :product="$product"
                    class="aspect-[4/3] w-full bg-gray-100 object-cover sm:aspect-square"
                />
            </div>

            @if ($product->description)
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/60 to-white px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                                <i class="ri-file-text-line text-lg" aria-hidden="true"></i>
                            </span>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">About this product</h2>
                                <p class="text-base text-gray-500">Details from the store.</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-5 py-5 text-base leading-relaxed text-gray-700 sm:px-6 sm:py-6">
                        {{ $product->description }}
                    </div>
                </section>
            @endif
        </div>

        <aside class="space-y-6 lg:sticky lg:top-24">
            <section
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white"
                data-product-purchase
                data-unit-price="{{ $product->price }}"
            >
                <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-5 py-5 sm:px-6">
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($product->category)
                            <a
                                href="{{ route('products.index', ['category' => $product->category_id]) }}"
                                class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700 transition hover:bg-brand-100"
                            >
                                <i class="ri-price-tag-3-line" aria-hidden="true"></i>
                                {{ $product->category->name }}
                            </a>
                        @endif

                        @if ($lowStock)
                            <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-3 py-1 text-sm font-semibold text-orange-700">
                                <i class="ri-error-warning-line" aria-hidden="true"></i>
                                Low stock
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700">
                                <i class="ri-checkbox-circle-line" aria-hidden="true"></i>
                                In stock
                            </span>
                        @endif
                    </div>

                    <h1 class="mt-4 text-2xl font-bold text-gray-900 sm:text-3xl">{{ $product->name }}</h1>

                    <a
                        href="{{ route('stores.show', $product->store) }}"
                        class="mt-2 inline-flex items-center gap-1.5 text-base font-medium text-brand-600 hover:text-brand-700"
                    >
                        <i class="ri-store-2-line" aria-hidden="true"></i>
                        {{ $product->store?->store_name }}
                    </a>

                    <p class="mt-4 text-3xl font-bold text-brand-600">₱{{ number_format($product->price, 2) }}</p>
                    <p class="mt-1 text-base text-gray-500">{{ $product->stock }} available</p>
                </div>

                <x-products.purchase-panel :product="$product" />
            </section>

            @if ($product->store)
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/60 to-white px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                                <i class="ri-store-2-line text-lg" aria-hidden="true"></i>
                            </span>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Sold by</h2>
                                <p class="text-base text-gray-500">Visit the store page for more items.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4 px-5 py-5 sm:px-6">
                        <div>
                            <p class="text-base font-semibold text-gray-900">{{ $product->store->store_name }}</p>
                            @if ($product->store->description)
                                <p class="mt-2 text-base leading-relaxed text-gray-600">{{ $product->store->description }}</p>
                            @endif
                        </div>
                        <a
                            href="{{ route('stores.show', $product->store) }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-base font-semibold text-gray-700 transition hover:bg-gray-50"
                        >
                            <i class="ri-store-2-line" aria-hidden="true"></i>
                            View store
                        </a>
                    </div>
                </section>
            @endif
        </aside>
    </div>
</div>
@endsection
