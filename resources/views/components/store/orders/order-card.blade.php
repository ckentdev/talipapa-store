@props(['order'])

@php
    $itemCount = $order->items->sum('quantity');
    $previewItems = $order->items->take(4);
    $extraItems = max(0, $order->items->count() - 4);
@endphp

<a
    href="{{ route('store.orders.show', $order) }}"
    class="group block overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:border-brand-200 hover:shadow-md"
>
    <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/60 to-white px-5 py-4 sm:px-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="font-mono text-base font-bold text-gray-900">{{ $order->order_number }}</p>
                <p class="mt-1 flex items-center gap-1.5 text-base text-gray-600">
                    <i class="ri-user-3-line shrink-0 text-brand-600" aria-hidden="true"></i>
                    <span class="truncate">{{ $order->customer?->name ?? 'Customer' }}</span>
                </p>
            </div>
            <x-order-status-badge :status="$order->status" class="px-3 py-1.5 text-sm" />
        </div>
    </div>

    <div class="px-5 py-4 sm:px-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center">
                @foreach ($previewItems as $item)
                    @if ($item->product)
                        <x-product-image
                            :product="$item->product"
                            class="h-12 w-12 rounded-lg border-2 border-white bg-gray-100 object-cover ring-1 ring-gray-100 {{ ! $loop->first ? '-ms-3' : '' }}"
                        />
                    @else
                        <span class="{{ ! $loop->first ? '-ms-3' : '' }} flex h-12 w-12 items-center justify-center rounded-lg border-2 border-white bg-gray-100 text-gray-400 ring-1 ring-gray-100">
                            <i class="ri-image-line" aria-hidden="true"></i>
                        </span>
                    @endif
                @endforeach
                @if ($extraItems > 0)
                    <span class="-ms-3 flex h-12 w-12 items-center justify-center rounded-lg border-2 border-white bg-gray-100 text-sm font-semibold text-gray-600 ring-1 ring-gray-100">
                        +{{ $extraItems }}
                    </span>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <p class="text-base font-medium text-gray-900">
                    {{ $itemCount }} {{ str('item')->plural($itemCount) }}
                </p>
                <p class="truncate text-base text-gray-500">
                    {{ $order->items->pluck('product.name')->filter()->take(2)->implode(', ') }}
                    @if ($order->items->count() > 2)
                        <span class="text-gray-400">and more</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4">
            <div>
                <p class="text-base text-gray-500">{{ $order->created_at->format('M d, Y · h:i A') }}</p>
                <p class="mt-0.5 text-lg font-bold text-brand-600">₱{{ number_format($order->total, 2) }}</p>
            </div>
            <span class="inline-flex items-center gap-1 text-base font-semibold text-brand-600 transition group-hover:gap-2">
                View details
                <i class="ri-arrow-right-line" aria-hidden="true"></i>
            </span>
        </div>
    </div>
</a>
