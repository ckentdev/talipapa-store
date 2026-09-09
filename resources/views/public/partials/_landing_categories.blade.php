@php
    $visibleLimit = 8;
    $hasMore = $categories->count() > $visibleLimit;
@endphp

<section class="border-y border-avocado-100/80 bg-white py-8 sm:py-10" data-category-section>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="mb-1 text-xs font-bold uppercase tracking-wider text-brand-600">Browse</p>
                <h2 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">Shop by Category</h2>
            </div>

            @if ($hasMore)
                <button
                    type="button"
                    data-category-toggle
                    aria-expanded="false"
                    class="hidden shrink-0 items-center gap-1 text-sm font-semibold text-forest-600 hover:text-brand-600 sm:inline-flex"
                >
                    <span data-toggle-label>View all</span>
                    <i data-toggle-icon class="ri-arrow-right-s-line text-lg" aria-hidden="true"></i>
                </button>
            @else
                <a href="{{ route('products.index') }}" class="hidden shrink-0 items-center gap-1 text-sm font-semibold text-forest-600 hover:text-brand-600 sm:inline-flex">
                    View all
                    <i class="ri-arrow-right-s-line text-lg" aria-hidden="true"></i>
                </a>
            @endif
        </div>

        @if ($categories->isEmpty())
            <p class="text-gray-500">No categories available yet.</p>
        @else
            {{-- Mobile: swipe through all categories --}}
            <ul class="-mx-1 flex snap-x snap-mandatory gap-4 overflow-x-auto px-1 pb-3 sm:hidden [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @foreach ($categories as $category)
                    <li class="w-[5.75rem] shrink-0 snap-start">
                        <a href="{{ route('products.index', ['category' => $category->id]) }}" class="group flex w-full flex-col items-center text-center">
                            <div class="mb-2.5 flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white shadow-md ring-1 ring-gray-100">
                                <x-img
                                    :src="$category->imageUrl()"
                                    :alt="$category->name"
                                    type="category"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                />
                            </div>
                            <span class="block w-full truncate text-xs font-semibold text-gray-900" title="{{ $category->name }}">{{ $category->name }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <p class="mb-0 flex items-center justify-center gap-1 text-xs font-medium text-gray-500 sm:hidden">
                <i class="ri-arrow-left-right-line" aria-hidden="true"></i>
                Swipe to see all categories
            </p>

            {{-- Desktop: show limited, expand on View all --}}
            <ul class="hidden flex-wrap justify-center gap-x-8 gap-y-8 sm:flex lg:gap-x-10">
                @foreach ($categories as $index => $category)
                    <li
                        @class([
                            'w-[6.5rem] shrink-0',
                            'hidden' => $hasMore && $index >= $visibleLimit,
                        ])
                        @if ($hasMore && $index >= $visibleLimit) data-category-hidden @endif
                    >
                        <a href="{{ route('products.index', ['category' => $category->id]) }}" class="group flex w-full flex-col items-center text-center">
                            <div class="mb-3 flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white shadow-md ring-1 ring-gray-100 transition group-hover:-translate-y-0.5 group-hover:shadow-lg">
                                <x-img
                                    :src="$category->imageUrl()"
                                    :alt="$category->name"
                                    type="category"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                />
                            </div>
                            <span class="block w-full truncate text-sm font-semibold text-gray-900" title="{{ $category->name }}">{{ $category->name }}</span>
                            @if ($category->products_count > 0)
                                <span class="mt-1 text-[11px] font-medium text-gray-500">{{ $category->products_count }} items</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>
