@props(['product', 'class' => 'w-full h-40 object-cover rounded-lg bg-gray-100'])

<x-img
    :src="$product->imageUrl()"
    :alt="$product->name"
    type="product"
    {{ $attributes->merge(['class' => $class]) }}
/>
