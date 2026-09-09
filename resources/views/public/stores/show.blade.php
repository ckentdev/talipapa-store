@extends('layouts.marketplace')

@section('title', $store->store_name)

@section('content')
<div class="mb-8">
    <a href="{{ route('stores.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back to stores</a>
    <h1 class="text-3xl font-bold mt-2">{{ $store->store_name }}</h1>
    <p class="text-gray-600 mt-2">{{ $store->description }}</p>
</div>

<h2 class="text-xl font-semibold mb-4">Products</h2>
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse ($store->products as $product)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <a href="{{ route('products.show', $product) }}">
                <x-product-image :product="$product" />
            </a>
            <div class="p-4">
                <a href="{{ route('products.show', $product) }}" class="font-medium hover:text-brand-600">{{ $product->name }}</a>
                <p class="text-brand-600 font-semibold mt-1">₱{{ number_format($product->price, 2) }}</p>
                <form method="POST" action="{{ route('cart.store') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="w-full text-sm bg-brand-600 text-white py-2 rounded-lg hover:bg-brand-700">Add to Cart</button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-gray-500 col-span-full">This store has no products yet.</p>
    @endforelse
</div>
@endsection
