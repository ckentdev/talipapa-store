@extends('layouts.store')

@section('title', 'Products & Inventory')

@section('content')
@php
    use App\Enums\ApprovalStatus;

    $isApproved = $store->status === ApprovalStatus::Approved;

    $kpiCards = [
        [
            'label' => 'Total Products',
            'value' => $productStats['total'],
            'icon' => 'ri-shopping-basket-line',
            'iconBg' => 'bg-forest-100 text-forest-700',
            'valueClass' => 'text-gray-900',
            'hint' => 'In your catalog',
        ],
        [
            'label' => 'Available',
            'value' => $productStats['available'],
            'icon' => 'ri-checkbox-circle-line',
            'iconBg' => 'bg-green-100 text-green-700',
            'valueClass' => 'text-green-700',
            'hint' => 'Visible to customers',
        ],
        [
            'label' => 'Hidden',
            'value' => $productStats['hidden'],
            'icon' => 'ri-eye-off-line',
            'iconBg' => 'bg-gray-100 text-gray-600',
            'valueClass' => 'text-gray-700',
            'hint' => 'Not listed publicly',
        ],
        [
            'label' => 'Out of Stock',
            'value' => $productStats['out_of_stock'],
            'icon' => 'ri-close-circle-line',
            'iconBg' => 'bg-red-100 text-red-700',
            'valueClass' => 'text-red-700',
            'hint' => 'Needs restocking',
        ],
        [
            'label' => 'Low Stock',
            'value' => $productStats['low_stock'],
            'icon' => 'ri-error-warning-line',
            'iconBg' => 'bg-orange-100 text-orange-700',
            'valueClass' => 'text-orange-700',
            'hint' => '5 units or less',
        ],
        [
            'label' => 'Inventory Value',
            'value' => '₱'.number_format($productStats['inventory_value'], 2),
            'icon' => 'ri-money-dollar-circle-line',
            'iconBg' => 'bg-brand-100 text-brand-700',
            'valueClass' => 'text-brand-700',
            'hint' => 'Price × stock total',
        ],
    ];
@endphp

{{-- KPIs --}}
<section class="mb-6">
    <div class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Product Overview</h2>
            <p class="text-1xl text-gray-500">Catalog health at a glance</p>
        </div>

        @if ($isApproved)
            <a
                href="{{ route('store.products.create') }}"
                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-forest-600 text-white shadow-sm transition hover:bg-forest-700"
                title="Add product"
                aria-label="Add product"
            >
                <i class="ri-add-line text-xl" aria-hidden="true"></i>
            </a>
        @endif
    </div>

    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3 xl:grid-cols-6">
        @foreach ($kpiCards as $card)
            <article class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-avocado-200 hover:shadow-md sm:p-5">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-base font-medium text-gray-500">{{ $card['label'] }}</p>
                        <p @class(['mt-1.5 text-base font-bold tracking-tight', $card['valueClass']])>{{ $card['value'] }}</p>
                        <p class="mt-0.5 text-base text-gray-500">{{ $card['hint'] }}</p>
                    </div>
                    <span @class(['inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl sm:h-10 sm:w-10', $card['iconBg']])>
                        <i class="{{ $card['icon'] }} text-lg" aria-hidden="true"></i>
                    </span>
                </div>
            </article>
        @endforeach
    </div>
</section>

{{-- Product grid --}}
@if ($products->isEmpty())
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        @include('layouts.partials._empty_state', [
            'title' => 'No products yet',
            'message' => $isApproved
                ? 'Add your first product to start selling.'
                : 'Products will appear here once your store is approved.',
            'icon' => '<i class="ri-shopping-basket-line text-2xl" aria-hidden="true"></i>',
        ])

        @if ($isApproved)
            <div class="mt-4 flex justify-center">
                <a
                    href="{{ route('store.products.create') }}"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-forest-600 text-white shadow-sm transition hover:bg-forest-700"
                    title="Add product"
                    aria-label="Add product"
                >
                    <i class="ri-add-line text-xl" aria-hidden="true"></i>
                </a>
            </div>
        @endif
    </div>
@else
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 xl:gap-4">
        @foreach ($products as $product)
            <article class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:border-avocado-200 hover:shadow-md">
                <div class="relative aspect-square overflow-hidden bg-gray-100">
                    <x-product-image
                        :product="$product"
                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                    />

                    <span @class([
                        'absolute left-2 top-2 inline-flex h-6 w-6 items-center justify-center rounded-full shadow-sm',
                        'bg-green-500 text-white' => $product->is_available,
                        'bg-gray-500 text-white' => ! $product->is_available,
                    ]) title="{{ $product->is_available ? 'Available' : 'Hidden' }}">
                        <i @class([
                            'text-xs',
                            'ri-eye-line' => $product->is_available,
                            'ri-eye-off-line' => ! $product->is_available,
                        ]) aria-hidden="true"></i>
                        <span class="sr-only">{{ $product->is_available ? 'Available' : 'Hidden' }}</span>
                    </span>

                    @if ($product->stock <= 0)
                        <span class="absolute bottom-2 left-2 rounded-md bg-red-600/90 px-1.5 py-0.5 text-sm font-semibold text-white">
                            Out of stock
                        </span>
                    @elseif ($product->stock <= 5)
                        <span class="absolute bottom-2 left-2 rounded-md bg-orange-500/90 px-1.5 py-0.5 text-sm font-semibold text-white">
                            Low stock
                        </span>
                    @endif
                </div>

                <div class="flex flex-1 flex-col p-3">
                    <h3 class="line-clamp-2 text-base font-semibold leading-snug text-gray-900" title="{{ $product->name }}">
                        {{ $product->name }}
                    </h3>

                    @if ($product->category)
                        <p class="mt-1 truncate text-base text-gray-500">{{ $product->category->name }}</p>
                    @endif

                    @if (filled($product->barcode))
                        <p class="mt-1 truncate text-sm text-gray-400">
                            <i class="ri-barcode-line mr-1" aria-hidden="true"></i>{{ $product->barcode }}
                        </p>
                    @endif

                    <div class="mt-auto flex items-center justify-between gap-2 pt-2">
                        <div class="flex min-w-0 flex-wrap items-center gap-x-3 gap-y-1">
                            <p class="text-base text-gray-500">
                                <i class="ri-stack-line mr-1" aria-hidden="true"></i>{{ $product->stock }}
                            </p>
                            <p class="truncate text-base font-bold text-brand-700">₱{{ number_format($product->price, 2) }}</p>
                        </div>

                        @if ($isApproved)
                            <div class="flex shrink-0 items-center gap-1">
                                <a
                                    href="{{ route('store.products.edit', $product) }}"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700"
                                    title="Edit product"
                                    aria-label="Edit {{ $product->name }}"
                                >
                                    <i class="ri-pencil-line text-sm" aria-hidden="true"></i>
                                </a>

                                <form id="delete-product-{{ $product->id }}" method="POST" action="{{ route('store.products.destroy', $product) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        data-confirm-trigger
                                        data-confirm-form="#delete-product-{{ $product->id }}"
                                        data-confirm-title="Delete product"
                                        data-confirm-message="Are you sure you want to delete {{ $product->name }}? This action cannot be undone."
                                        data-confirm-label="Delete"
                                        data-confirm-variant="danger"
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-red-200 text-red-600 transition hover:bg-red-50"
                                        title="Delete product"
                                        aria-label="Delete {{ $product->name }}"
                                    >
                                        <i class="ri-delete-bin-line text-sm" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
@endif
@endsection
