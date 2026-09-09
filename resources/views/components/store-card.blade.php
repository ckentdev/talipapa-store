@props([
    'store',
])

@php
    use Illuminate\Support\Str;

    $productCount = $store->products_count ?? $store->products()->count();
    $hasCustomLogo = filled($store->logo_path);
@endphp

<article {{ $attributes->merge(['class' => 'group flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:border-avocado-200 hover:shadow-lg']) }}>
    <a href="{{ route('stores.show', $store) }}" class="flex h-full flex-col">
        <div class="relative aspect-[16/10] overflow-hidden bg-avocado-100">
            <x-img
                :src="$store->coverUrl()"
                :alt="$store->store_name.' cover'"
                type="store_cover"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>

            <div class="absolute bottom-3 left-3 flex h-12 w-12 items-center justify-center overflow-hidden rounded-xl bg-white shadow-md ring-2 ring-white">
                @if ($hasCustomLogo)
                    <x-img
                        :src="$store->logoUrl()"
                        :alt="$store->store_name"
                        type="store_logo"
                        class="h-full w-full object-cover"
                    />
                @else
                    <span class="text-base font-bold text-forest-600">{{ strtoupper(substr($store->store_name, 0, 1)) }}</span>
                @endif
            </div>

            @if ($productCount > 0)
                <span class="absolute right-3 top-3 rounded-full bg-white/95 px-2.5 py-1 text-[11px] font-semibold text-gray-700 shadow-sm">
                    {{ $productCount }} {{ Str::plural('item', $productCount) }}
                </span>
            @endif
        </div>

        <div class="flex flex-1 flex-col p-4">
            <h3 class="truncate text-base font-bold text-gray-900 group-hover:text-brand-600">
                {{ $store->store_name }}
            </h3>

            @if ($store->description)
                <p class="mt-1 line-clamp-2 text-sm text-gray-500">
                    {{ $store->description }}
                </p>
            @endif

            @if ($store->addresses->first())
                <p class="mt-2 flex items-start gap-1 text-xs text-gray-400">
                    <i class="ri-map-pin-line mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span class="line-clamp-1">{{ $store->addresses->first()->fullAddress() }}</span>
                </p>
            @endif

            <span class="mt-auto inline-flex items-center gap-1 pt-3 text-sm font-semibold text-forest-600">
                Visit store
                <i class="ri-arrow-right-s-line transition group-hover:translate-x-0.5" aria-hidden="true"></i>
            </span>
        </div>
    </a>
</article>
