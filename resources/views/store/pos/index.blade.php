@extends('layouts.store')

@section('title', 'In App Point of Sales')

@section('content')
@php
    use App\Enums\ApprovalStatus;

    $isApproved = $store->status === ApprovalStatus::Approved;
@endphp

@if (session('success'))
    <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
@endif

@if (! $isApproved)
    <x-alert type="warning" title="Store approval required" class="mb-6">
        Point of Sales is available once your store is approved.
    </x-alert>
@elseif (! $hasStoreAddress)
    <x-alert type="warning" title="Store address required" class="mb-6">
        Add your store location in
        <a href="{{ route('store.information') }}" class="font-semibold underline">Store Information</a>
        before ringing up sales.
    </x-alert>
@endif

<section
    x-data="storePos(@js($products))"
    class="flex min-h-[calc(100vh-12rem)] flex-col gap-4 sm:gap-6 xl:flex-row"
>
    {{-- Product catalog --}}
    <div class="min-w-0 flex-1">
        {{-- Header --}}
        <div class="mb-3 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm sm:mb-5 sm:rounded-2xl">
            <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-3 py-3 sm:px-6 sm:py-4">
                <div class="flex flex-col gap-3 sm:gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-2.5 sm:gap-3">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-forest-600/10 text-forest-600 sm:h-11 sm:w-11 sm:rounded-xl">
                            <i class="ri-cash-line text-lg sm:text-xl" aria-hidden="true"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-brand-600 sm:text-xs">In App POS</p>
                            <h1 class="text-lg font-bold text-gray-900 sm:text-2xl">Point of Sales</h1>
                            <p class="hidden text-sm text-gray-500 sm:block">Ring up walk-in customers at your store.</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <div
                            class="inline-flex rounded-xl border border-gray-200 bg-gray-50 p-1"
                            role="group"
                            aria-label="Product view mode"
                        >
                            <button
                                type="button"
                                @click="viewMode = 'grid'"
                                :class="viewMode === 'grid'
                                    ? 'bg-white text-forest-700 shadow-sm ring-1 ring-gray-200'
                                    : 'text-gray-500 hover:text-gray-700'"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg transition"
                                title="Grid view"
                                aria-label="Grid view"
                                :aria-pressed="viewMode === 'grid'"
                            >
                                <i class="ri-grid-fill text-lg" aria-hidden="true"></i>
                            </button>
                            <button
                                type="button"
                                @click="viewMode = 'list'"
                                :class="viewMode === 'list'
                                    ? 'bg-white text-forest-700 shadow-sm ring-1 ring-gray-200'
                                    : 'text-gray-500 hover:text-gray-700'"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg transition"
                                title="List view"
                                aria-label="List view"
                                :aria-pressed="viewMode === 'list'"
                            >
                                <i class="ri-list-check-2 text-lg" aria-hidden="true"></i>
                            </button>
                        </div>

                        <div class="relative min-w-[12rem] flex-1 sm:max-w-xs">
                            <label for="pos-search" class="sr-only">Search products</label>
                            <i class="ri-search-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true"></i>
                            <input
                                id="pos-search"
                                type="search"
                                x-model="search"
                                placeholder="Search name or barcode..."
                                class="block w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm focus:border-forest-600 focus:ring-forest-600"
                                @disabled(! $isApproved)
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2 px-3 py-2 sm:px-6 sm:py-3">
                <p class="text-xs text-gray-500 sm:text-sm">
                    <span class="font-semibold text-gray-900" x-text="filteredProducts.length"></span>
                    product<span x-show="filteredProducts.length !== 1">s</span> available
                </p>
                <p class="text-xs text-gray-400" x-show="search.trim()">
                    Showing results for “<span x-text="search.trim()"></span>”
                </p>
            </div>
        </div>

        {{-- Grid view --}}
        <div
            x-show="viewMode === 'grid' && filteredProducts.length > 0"
            x-cloak
            class="grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-3 lg:grid-cols-3 xl:grid-cols-4"
        >
            <template x-for="product in filteredProducts" :key="'grid-' + product.id">
                <article class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:border-forest-200 hover:shadow-md sm:rounded-2xl">
                    <div class="relative aspect-square overflow-hidden bg-gray-100">
                        <img
                            :src="product.image_url"
                            :alt="product.name"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        >
                        <span
                            x-show="quantityInCart(product.id) > 0"
                            x-cloak
                            class="absolute right-1.5 top-1.5 inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-forest-600 px-1.5 py-0.5 text-[10px] font-bold text-white shadow-sm sm:right-2 sm:top-2"
                            x-text="`${quantityInCart(product.id)} in cart`"
                        ></span>
                    </div>
                    <div class="flex flex-1 flex-col p-2 sm:p-3">
                        <p class="line-clamp-2 text-xs font-semibold leading-snug text-gray-900 sm:text-sm" x-text="product.name"></p>
                        <p class="mt-0.5 truncate text-[10px] text-gray-400 sm:mt-1 sm:text-xs" x-text="product.category ?? 'Uncategorized'"></p>
                        <p class="mt-1 text-sm font-bold text-brand-700 sm:mt-2 sm:text-base" x-text="formatMoney(product.price)"></p>
                        <div class="mt-auto w-full space-y-1.5 pt-2 sm:space-y-0">
                            <div class="flex w-full items-center gap-1.5 sm:gap-2 sm:pt-0">
                            <span class="shrink-0 text-[10px] font-medium text-gray-500 sm:text-xs">
                                <span x-text="product.stock"></span> left
                            </span>
                            <div class="flex min-w-0 flex-1 items-center rounded-lg border border-gray-200 bg-gray-50 p-0.5 sm:rounded-xl">
                                <button
                                    type="button"
                                    @click="decrementAddQuantity(product)"
                                    :disabled="!canUsePos || getAddQuantity(product.id) <= 1"
                                    class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-gray-600 transition hover:bg-white hover:text-forest-700 disabled:opacity-40 sm:h-8 sm:w-8 sm:rounded-lg"
                                    aria-label="Decrease add quantity"
                                >
                                    <i class="ri-subtract-line text-sm" aria-hidden="true"></i>
                                </button>
                                <input
                                    type="number"
                                    :value="getAddQuantity(product.id)"
                                    @input="setAddQuantity(product.id, product, $event.target.value)"
                                    min="1"
                                    :max="maxAddQuantity(product)"
                                    class="min-w-0 flex-1 border-0 bg-transparent p-0 text-center text-xs font-bold text-gray-900 focus:ring-0 sm:text-sm"
                                    :disabled="!canUsePos || maxAddQuantity(product) <= 0"
                                    aria-label="Quantity to add"
                                >
                                <button
                                    type="button"
                                    @click="incrementAddQuantity(product)"
                                    :disabled="!canUsePos || getAddQuantity(product.id) >= maxAddQuantity(product)"
                                    class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-gray-600 transition hover:bg-white hover:text-forest-700 disabled:opacity-40 sm:h-8 sm:w-8 sm:rounded-lg"
                                    aria-label="Increase add quantity"
                                >
                                    <i class="ri-add-line text-sm" aria-hidden="true"></i>
                                </button>
                            </div>
                            <button
                                type="button"
                                @click="addToCart(product)"
                                :disabled="!canUsePos || maxAddQuantity(product) <= 0"
                                class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-forest-600 text-white shadow-sm transition hover:bg-forest-700 disabled:cursor-not-allowed disabled:opacity-50 sm:h-9 sm:w-9 sm:rounded-xl"
                                aria-label="Add to sale"
                            >
                                <i class="ri-add-line text-sm sm:text-base" aria-hidden="true"></i>
                            </button>
                            </div>
                        </div>
                    </div>
                </article>
            </template>
        </div>

        {{-- List view --}}
        <div
            x-show="viewMode === 'list' && filteredProducts.length > 0"
            x-cloak
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
        >
            <div class="hidden border-b border-gray-100 bg-gray-50 px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:grid sm:grid-cols-[minmax(0,1fr)_6rem_4rem_7rem_5rem] sm:gap-4 sm:px-5">
                <span>Product</span>
                <span class="text-right">Price</span>
                <span class="text-center">Stock</span>
                <span class="text-center">Qty</span>
                <span class="text-right">Action</span>
            </div>

            <div class="divide-y divide-gray-100">
                <template x-for="product in filteredProducts" :key="'list-' + product.id">
                    <div class="flex flex-col gap-2 px-3 py-2.5 transition hover:bg-avocado-50/30 sm:grid sm:grid-cols-[minmax(0,1fr)_6rem_4rem_7rem_5rem] sm:items-center sm:gap-4 sm:px-5 sm:py-3.5">
                        <div class="flex min-w-0 items-center gap-2.5 sm:gap-3">
                            <div class="h-11 w-11 shrink-0 overflow-hidden rounded-lg bg-gray-100 ring-1 ring-gray-200 sm:h-14 sm:w-14 sm:rounded-xl">
                                <img
                                    :src="product.image_url"
                                    :alt="product.name"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                >
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-gray-900" x-text="product.name"></p>
                                <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                    <p class="truncate text-xs text-gray-400" x-text="product.category ?? 'Uncategorized'"></p>
                                    <p class="text-sm font-bold text-brand-700 sm:hidden" x-text="formatMoney(product.price)"></p>
                                    <p x-show="quantityInCart(product.id) > 0" x-cloak class="text-[11px] font-medium text-forest-600">
                                        · <span x-text="quantityInCart(product.id)"></span> in cart
                                    </p>
                                </div>
                            </div>
                        </div>

                        <p class="hidden text-right text-sm font-bold text-brand-700 sm:block" x-text="formatMoney(product.price)"></p>

                        <p class="hidden text-center text-sm text-gray-600 sm:block">
                            <i class="ri-stack-line mr-1 text-gray-400" aria-hidden="true"></i>
                            <span x-text="product.stock"></span>
                        </p>

                        <div class="flex w-full items-center gap-2 sm:contents">
                            <span class="shrink-0 whitespace-nowrap text-[11px] font-medium text-gray-500 sm:hidden">
                                <span x-text="product.stock"></span> left
                            </span>

                            <div class="flex min-w-0 flex-1 items-center rounded-lg border border-gray-200 bg-gray-50 p-0.5 sm:w-auto sm:flex-none sm:rounded-xl">
                                <button
                                    type="button"
                                    @click="decrementAddQuantity(product)"
                                    :disabled="!canUsePos || getAddQuantity(product.id) <= 1"
                                    class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-gray-600 transition hover:bg-white hover:text-forest-700 disabled:opacity-40 sm:h-8 sm:w-8 sm:rounded-lg"
                                    aria-label="Decrease add quantity"
                                >
                                    <i class="ri-subtract-line text-sm" aria-hidden="true"></i>
                                </button>
                                <input
                                    type="number"
                                    :value="getAddQuantity(product.id)"
                                    @input="setAddQuantity(product.id, product, $event.target.value)"
                                    min="1"
                                    :max="maxAddQuantity(product)"
                                    class="min-w-0 flex-1 border-0 bg-transparent p-0 text-center text-xs font-bold text-gray-900 focus:ring-0 sm:w-10 sm:flex-none sm:text-sm"
                                    :disabled="!canUsePos || maxAddQuantity(product) <= 0"
                                    aria-label="Quantity to add"
                                >
                                <button
                                    type="button"
                                    @click="incrementAddQuantity(product)"
                                    :disabled="!canUsePos || getAddQuantity(product.id) >= maxAddQuantity(product)"
                                    class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-gray-600 transition hover:bg-white hover:text-forest-700 disabled:opacity-40 sm:h-8 sm:w-8 sm:rounded-lg"
                                    aria-label="Increase add quantity"
                                >
                                    <i class="ri-add-line text-sm" aria-hidden="true"></i>
                                </button>
                            </div>

                            <button
                                type="button"
                                @click="addToCart(product)"
                                :disabled="!canUsePos || maxAddQuantity(product) <= 0"
                                class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-forest-600 text-white shadow-sm transition hover:bg-forest-700 disabled:cursor-not-allowed disabled:opacity-50 sm:h-auto sm:w-auto sm:gap-1.5 sm:rounded-xl sm:px-3 sm:py-2"
                                aria-label="Add to sale"
                            >
                                <i class="ri-add-line text-sm sm:text-base" aria-hidden="true"></i>
                                <span class="hidden text-xs font-semibold sm:inline">Add</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Empty state --}}
        <div
            x-show="filteredProducts.length === 0"
            x-cloak
            class="rounded-2xl border border-dashed border-gray-200 bg-white p-8 text-center shadow-sm"
        >
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-400">
                <i class="ri-shopping-basket-line text-2xl" aria-hidden="true"></i>
            </span>
            <p class="mt-3 font-semibold text-gray-900">No products found</p>
            <p class="mt-1 text-sm text-gray-500">Add available products with stock to use Point of Sales.</p>
            @if ($isApproved)
                <a href="{{ route('store.products') }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700">
                    Manage products
                    <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                </a>
            @endif
        </div>
    </div>

    {{-- Current Sale --}}
    <aside class="w-full shrink-0 xl:w-[22rem] 2xl:w-96">
        <div class="sticky top-20 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm sm:top-24 sm:rounded-2xl">
            {{-- Header --}}
            <div class="relative overflow-hidden border-b border-forest-700/20 bg-gradient-to-br from-forest-700 to-forest-800 px-4 py-3 text-white sm:px-5 sm:py-4">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
                <div class="relative flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-forest-100">Current Sale</p>
                        <p class="mt-0.5 text-xl font-bold tracking-tight sm:mt-1 sm:text-2xl" x-text="formatMoney(totalDue)"></p>
                        <p class="mt-0.5 text-xs text-forest-100/90">
                            <span x-text="cartCount"></span>
                            <span x-text="cartCount === 1 ? 'item' : 'items'"></span>
                            <span x-show="discountAmount > 0" x-cloak>
                                · <span x-text="`-${formatMoney(discountAmount)} off`"></span>
                            </span>
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="clearCart()"
                        x-show="cart.length > 0"
                        x-cloak
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/15 text-white transition hover:bg-white/25 sm:h-auto sm:w-auto sm:gap-1 sm:px-2.5 sm:py-1.5"
                        aria-label="Clear sale"
                    >
                        <i class="ri-delete-bin-line" aria-hidden="true"></i>
                        <span class="hidden text-xs font-semibold sm:inline">Clear</span>
                    </button>
                </div>
            </div>

            {{-- Line items --}}
            <div class="max-h-[18rem] overflow-y-auto">
                <template x-if="cart.length === 0">
                    <div class="flex flex-col items-center px-5 py-10 text-center">
                        <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400">
                            <i class="ri-shopping-cart-line text-2xl" aria-hidden="true"></i>
                        </span>
                        <p class="mt-3 text-sm font-semibold text-gray-900">No items yet</p>
                        <p class="mt-1 max-w-[14rem] text-xs text-gray-500">Select products from the catalog to start a sale.</p>
                    </div>
                </template>

                <div x-show="cart.length > 0" class="divide-y divide-gray-100">
                    <template x-for="item in cart" :key="item.id">
                        <article class="flex gap-3 px-4 py-3">
                            <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-gray-100 ring-1 ring-gray-200">
                                <img
                                    :src="item.image_url"
                                    :alt="item.name"
                                    class="h-full w-full object-cover"
                                >
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="line-clamp-2 text-sm font-semibold leading-snug text-gray-900" x-text="item.name"></p>
                                    <button
                                        type="button"
                                        @click="removeItem(item.id)"
                                        class="shrink-0 text-gray-300 transition hover:text-red-500"
                                        aria-label="Remove item"
                                    >
                                        <i class="ri-close-line text-lg" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <p class="mt-0.5 text-xs text-gray-500" x-text="`${formatMoney(item.price)} each`"></p>
                                <div class="mt-2 flex items-center gap-2">
                                    <div class="flex min-w-0 flex-1 items-center rounded-lg border border-gray-200 bg-gray-50 p-0.5">
                                        <button
                                            type="button"
                                            @click="decrementItem(item.id)"
                                            class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-gray-600 transition hover:bg-white hover:text-forest-700"
                                            aria-label="Decrease quantity"
                                        >
                                            <i class="ri-subtract-line" aria-hidden="true"></i>
                                        </button>
                                        <span class="min-w-0 flex-1 text-center text-sm font-bold text-gray-900" x-text="item.quantity"></span>
                                        <button
                                            type="button"
                                            @click="incrementItem(item.id)"
                                            :disabled="item.quantity >= item.stock"
                                            class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-gray-600 transition hover:bg-white hover:text-forest-700 disabled:opacity-40"
                                            aria-label="Increase quantity"
                                        >
                                            <i class="ri-add-line" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <p class="shrink-0 text-sm font-bold text-gray-900" x-text="formatMoney(item.price * item.quantity)"></p>
                                </div>
                            </div>
                        </article>
                    </template>
                </div>
            </div>

            {{-- Discount & totals --}}
            <div x-show="cart.length > 0" x-cloak class="border-t border-gray-100 bg-white px-4 py-3">
                <div class="rounded-xl border border-dashed border-amber-200 bg-amber-50/60 p-3">
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wide text-amber-800">Discount</span>
                        <div class="inline-flex rounded-lg border border-amber-200 bg-white p-0.5">
                            <button
                                type="button"
                                @click="discountType = 'fixed'"
                                :class="discountType === 'fixed' ? 'bg-amber-500 text-white shadow-sm' : 'text-amber-700 hover:bg-amber-50'"
                                class="inline-flex h-7 min-w-[2rem] items-center justify-center rounded-md px-2 text-xs font-bold transition"
                            >
                                ₱
                            </button>
                            <button
                                type="button"
                                @click="discountType = 'percent'"
                                :class="discountType === 'percent' ? 'bg-amber-500 text-white shadow-sm' : 'text-amber-700 hover:bg-amber-50'"
                                class="inline-flex h-7 min-w-[2rem] items-center justify-center rounded-md px-2 text-xs font-bold transition"
                            >
                                %
                            </button>
                        </div>
                    </div>
                    <div class="relative">
                        <span
                            class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-amber-700"
                            x-text="discountType === 'percent' ? '%' : '₱'"
                        ></span>
                        <input
                            type="number"
                            x-model="discountValue"
                            min="0"
                            step="0.01"
                            :max="discountType === 'percent' ? 100 : cartTotal"
                            placeholder="0.00"
                            class="block w-full rounded-lg border border-amber-200 bg-white py-2 pl-8 pr-3 text-sm focus:border-amber-400 focus:ring-amber-400"
                            @disabled(! $isApproved)
                        >
                    </div>
                </div>

                <div class="mt-3 space-y-1.5 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm">
                    <div class="flex items-center justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span x-text="formatMoney(cartTotal)"></span>
                    </div>
                    <div x-show="discountAmount > 0" class="flex items-center justify-between text-amber-700">
                        <span>Discount</span>
                        <span x-text="`-${formatMoney(discountAmount)}`"></span>
                    </div>
                    <div class="flex items-center justify-between border-t border-dashed border-gray-200 pt-1.5 font-semibold text-gray-900">
                        <span>Total due</span>
                        <span class="text-base text-brand-700" x-text="formatMoney(totalDue)"></span>
                    </div>
                </div>
            </div>

            {{-- Checkout --}}
            <form id="pos-checkout-form" method="POST" action="{{ route('store.pos.checkout') }}" class="border-t border-gray-100 bg-gray-50/80 p-3 sm:p-4">
                @csrf
                <input type="hidden" name="items" :value="JSON.stringify(cartPayload)">
                <input type="hidden" name="discount_type" :value="discountTypeForSubmit">
                <input type="hidden" name="discount_value" :value="discountValueForSubmit">

                <div class="space-y-3">
                    <div>
                        <label for="customer_name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Customer</label>
                        <input
                            id="customer_name"
                            type="text"
                            name="customer_name"
                            value="{{ old('customer_name') }}"
                            placeholder="Walk-in customer (optional)"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm focus:border-forest-600 focus:ring-forest-600"
                            @disabled(! $isApproved)
                        >
                    </div>

                    <x-pos.payment-methods :disabled="! $isApproved" />

                    @if ($errors->any() && ! $errors->has('cash_tendered'))
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <button
                        type="submit"
                        :disabled="!canCheckout"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-forest-600 px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-forest-700 disabled:cursor-not-allowed disabled:opacity-50"
                        aria-label="Complete sale"
                    >
                        <i class="ri-check-double-line text-lg" aria-hidden="true"></i>
                        <span class="hidden sm:inline">Complete sale</span>
                        <span x-show="cartCount > 0" x-cloak class="rounded-full bg-white/20 px-2 py-0.5 text-xs" x-text="cartCount"></span>
                    </button>
                </div>
            </form>
        </div>
    </aside>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('storePos', (products) => ({
            products,
            search: '',
            viewMode: 'list',
            cart: [],
            addQuantities: {},
            paymentMethod: @json(old('payment_method', 'cash') === 'cod' ? 'cash' : old('payment_method', 'cash')),
            discountType: @json(old('discount_type', 'fixed')),
            discountValue: @json(old('discount_value', '')),
            cashTendered: @json(old('cash_tendered', '')),
            canUsePos: @json($isApproved && $hasStoreAddress),

            init() {
                this.$nextTick(() => {
                    const form = document.getElementById('pos-checkout-form');
                    if (! form) {
                        return;
                    }

                    form.querySelectorAll('.pos-payment-method-radio').forEach((radio) => {
                        radio.addEventListener('change', () => {
                            this.paymentMethod = form.querySelector('.pos-payment-method-radio:checked')?.value ?? 'cash';
                        });
                    });

                    form.addEventListener('pos-payment-changed', (event) => {
                        this.paymentMethod = event.detail?.method ?? 'cash';
                    });
                });
            },

            get filteredProducts() {
                const query = this.search.trim().toLowerCase();

                if (! query) {
                    return this.products;
                }

                return this.products.filter((product) => {
                    return product.name.toLowerCase().includes(query)
                        || (product.category ?? '').toLowerCase().includes(query)
                        || (product.barcode ?? '').toLowerCase().includes(query);
                });
            },

            get cartCount() {
                return this.cart.reduce((total, item) => total + item.quantity, 0);
            },

            get cartTotal() {
                return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            },

            get discountAmount() {
                const value = Number(this.discountValue) || 0;

                if (value <= 0 || this.cartTotal <= 0) {
                    return 0;
                }

                if (this.discountType === 'percent') {
                    return Math.min(this.cartTotal, this.cartTotal * Math.min(value, 100) / 100);
                }

                return Math.min(this.cartTotal, value);
            },

            get totalDue() {
                return Math.max(0, this.cartTotal - this.discountAmount);
            },

            get cashTenderedAmount() {
                return Number(this.cashTendered) || 0;
            },

            get changeDue() {
                return Math.max(0, this.cashTenderedAmount - this.totalDue);
            },

            get discountTypeForSubmit() {
                const value = Number(this.discountValue) || 0;

                return value > 0 ? this.discountType : 'none';
            },

            get discountValueForSubmit() {
                const value = Number(this.discountValue) || 0;

                return value > 0 ? value : '';
            },

            get cartPayload() {
                return this.cart.map((item) => ({
                    product_id: item.id,
                    quantity: item.quantity,
                }));
            },

            get canCheckout() {
                if (! this.canUsePos || this.cart.length === 0) {
                    return false;
                }

                if (this.paymentMethod === 'cash') {
                    return this.cashTenderedAmount >= this.totalDue;
                }

                return true;
            },

            formatMoney(amount) {
                return `₱${Number(amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            },

            quantityInCart(productId) {
                const item = this.cart.find((entry) => entry.id === productId);

                return item ? item.quantity : 0;
            },

            maxAddQuantity(product) {
                return Math.max(0, product.stock - this.quantityInCart(product.id));
            },

            getAddQuantity(productId) {
                const stored = this.addQuantities[productId];

                return stored !== undefined && stored !== null && stored !== ''
                    ? Number(stored)
                    : 1;
            },

            setAddQuantity(productId, product, value) {
                const max = this.maxAddQuantity(product);

                if (max <= 0) {
                    this.addQuantities[productId] = 1;

                    return;
                }

                let qty = parseInt(String(value).replace(/\D/g, ''), 10);

                if (Number.isNaN(qty) || qty < 1) {
                    qty = 1;
                }

                this.addQuantities[productId] = Math.min(qty, max);
            },

            decrementAddQuantity(product) {
                const current = this.getAddQuantity(product.id);
                this.addQuantities[product.id] = Math.max(1, current - 1);
            },

            incrementAddQuantity(product) {
                const max = this.maxAddQuantity(product);
                const current = this.getAddQuantity(product.id);
                this.addQuantities[product.id] = Math.min(max, current + 1);
            },

            addToCart(product) {
                if (! this.canUsePos) {
                    return;
                }

                const max = this.maxAddQuantity(product);

                if (max <= 0) {
                    return;
                }

                const toAdd = Math.min(this.getAddQuantity(product.id), max);
                const existing = this.cart.find((item) => item.id === product.id);

                if (existing) {
                    existing.quantity += toAdd;
                } else {
                    this.cart.push({
                        id: product.id,
                        name: product.name,
                        price: product.price,
                        stock: product.stock,
                        image_url: product.image_url,
                        quantity: toAdd,
                    });
                }

                this.addQuantities[product.id] = 1;
            },

            incrementItem(productId) {
                const item = this.cart.find((entry) => entry.id === productId);

                if (item && item.quantity < item.stock) {
                    item.quantity += 1;
                }
            },

            decrementItem(productId) {
                const item = this.cart.find((entry) => entry.id === productId);

                if (! item) {
                    return;
                }

                if (item.quantity <= 1) {
                    this.removeItem(productId);

                    return;
                }

                item.quantity -= 1;
            },

            removeItem(productId) {
                this.cart = this.cart.filter((entry) => entry.id !== productId);
            },

            clearCart() {
                this.cart = [];
                this.addQuantities = {};
                this.discountValue = '';
                this.cashTendered = '';
            },
        }));
    });
</script>
@endpush
