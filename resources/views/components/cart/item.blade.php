@props(['item'])

@php
    $maxQty = min(99, (int) ($item->product->stock ?? 99));
@endphp

<article class="flex flex-col gap-4 border-b border-gray-100 px-5 py-5 last:border-b-0 sm:flex-row sm:px-6 sm:py-6">
    <a href="{{ route('products.show', $item->product) }}" class="shrink-0">
        <x-product-image
            :product="$item->product"
            class="h-24 w-24 rounded-xl bg-gray-100 object-cover ring-1 ring-gray-100 transition hover:ring-brand-200 sm:h-28 sm:w-28"
        />
    </a>

    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <a href="{{ route('products.show', $item->product) }}" class="text-base font-semibold text-gray-900 hover:text-brand-600">
                    {{ $item->product->name }}
                </a>
                <p class="mt-1 flex items-center gap-1.5 text-base text-gray-500">
                    <i class="ri-store-2-line shrink-0 text-brand-600" aria-hidden="true"></i>
                    {{ $item->product->store?->store_name }}
                </p>
            </div>
            <p class="text-lg font-bold text-gray-900">₱{{ number_format($item->total(), 2) }}</p>
        </div>

        <p class="mt-2 text-base text-brand-600">₱{{ number_format($item->unit_price, 2) }} each</p>

        <div class="mt-4 flex flex-wrap items-center gap-4">
            <div class="inline-flex items-center overflow-hidden rounded-lg border border-gray-300 bg-white shadow-sm">
                <form method="POST" action="{{ route('cart.update', $item) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="quantity" value="{{ max(0, $item->quantity - 1) }}">
                    <button
                        type="submit"
                        class="flex h-10 w-10 items-center justify-center text-gray-600 transition hover:bg-gray-50 hover:text-brand-600"
                        aria-label="Decrease quantity"
                    >
                        <i class="ri-subtract-line text-lg" aria-hidden="true"></i>
                    </button>
                </form>

                <form method="POST" action="{{ route('cart.update', $item) }}" class="border-x border-gray-300">
                    @csrf
                    @method('PATCH')
                    <label class="sr-only" for="qty-{{ $item->id }}">Quantity</label>
                    <input
                        id="qty-{{ $item->id }}"
                        type="number"
                        name="quantity"
                        value="{{ $item->quantity }}"
                        min="1"
                        max="{{ $maxQty }}"
                        class="h-10 w-14 border-0 bg-transparent text-center text-base font-semibold text-gray-900 focus:ring-0"
                        onchange="this.form.submit()"
                    >
                </form>

                <form method="POST" action="{{ route('cart.update', $item) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="quantity" value="{{ min($maxQty, $item->quantity + 1) }}">
                    <button
                        type="submit"
                        @disabled($item->quantity >= $maxQty)
                        class="flex h-10 w-10 items-center justify-center text-gray-600 transition hover:bg-gray-50 hover:text-brand-600 disabled:cursor-not-allowed disabled:opacity-40"
                        aria-label="Increase quantity"
                    >
                        <i class="ri-add-line text-lg" aria-hidden="true"></i>
                    </button>
                </form>
            </div>

            <form method="POST" action="{{ route('cart.destroy', $item) }}">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 text-base font-medium text-red-600 transition hover:text-red-700"
                >
                    <i class="ri-delete-bin-line" aria-hidden="true"></i>
                    Remove
                </button>
            </form>
        </div>
    </div>
</article>
