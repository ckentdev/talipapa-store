@props([
    'product',
])

@php
    $canAddToCart = $product->is_available && $product->stock > 0;
@endphp

<article {{ $attributes->merge(['class' => 'group flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md']) }}>
    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
        <a href="{{ route('products.show', $product) }}" class="block h-full w-full">
            <x-img
                :src="$product->imageUrl()"
                :alt="$product->name"
                type="product"
                class="h-full w-full object-cover transition-transform duration-300 hover:scale-105"
                loading="lazy"
            />
        </a>

        @unless ($canAddToCart)
            <span class="absolute left-2 top-2 rounded-md bg-gray-900/75 px-2 py-1 text-xs font-medium text-white">
                Unavailable
            </span>
        @endunless

        <form method="POST" action="{{ route('cart.store') }}" class="absolute right-2 top-2 z-10">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">

            <button
                type="submit"
                @disabled(! $canAddToCart)
                title="Add to Cart"
                aria-label="Add {{ $product->name }} to cart"
                @class([
                    'inline-flex h-9 w-9 items-center justify-center rounded-full shadow-md transition-colors focus:outline-none focus:ring-2 focus:ring-brand-600 focus:ring-offset-2',
                    'bg-white text-brand-600 hover:bg-brand-600 hover:text-white' => $canAddToCart,
                    'cursor-not-allowed bg-gray-100 text-gray-400' => ! $canAddToCart,
                ])
            >
                <i class="ri-shopping-cart-2-line text-lg" aria-hidden="true"></i>
            </button>
        </form>
    </div>

    <div class="flex flex-1 flex-col p-4">
        <a href="{{ route('stores.show', $product->store) }}" class="mb-1 text-xs font-medium text-brand-600 hover:text-brand-700">
            {{ $product->store->store_name }}
        </a>

        <h3 class="mb-1 line-clamp-2 text-sm font-semibold text-gray-900">
            <a href="{{ route('products.show', $product) }}" class="hover:text-brand-600">
                {{ $product->name }}
            </a>
        </h3>

        <p class="text-lg font-bold text-gray-900">
            ₱{{ number_format($product->price, 2) }}
        </p>
    </div>
</article>
