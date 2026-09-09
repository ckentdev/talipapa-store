<section class="relative overflow-hidden bg-avocado-50/30 py-10 sm:py-12">
    {{-- Grocery-themed decorative icons --}}
    <div class="pointer-events-none absolute left-4 top-6 opacity-[0.07] sm:left-10 sm:top-8" aria-hidden="true">
        <i class="ri-shopping-basket-line text-[5.5rem] text-forest-600 -rotate-6"></i>
    </div>

    <div class="pointer-events-none absolute right-6 top-8 opacity-[0.08] sm:right-16" aria-hidden="true">
        <i class="ri-apple-fill text-6xl text-brand-600 rotate-12"></i>
    </div>

    <div class="pointer-events-none absolute right-[10%] top-1/2 hidden -translate-y-1/2 opacity-[0.06] md:block" aria-hidden="true">
        <i class="ri-shopping-cart-2-line text-[4.5rem] text-forest-600 rotate-6"></i>
    </div>

    <div class="pointer-events-none absolute bottom-10 left-[18%] opacity-[0.07]" aria-hidden="true">
        <i class="ri-leaf-fill text-5xl text-brand-500 -rotate-12"></i>
    </div>

    <div class="pointer-events-none absolute bottom-8 right-[22%] hidden opacity-[0.06] sm:block" aria-hidden="true">
        <i class="ri-seedling-line text-[3.5rem] text-forest-600 rotate-3"></i>
    </div>

    <div class="pointer-events-none absolute left-[42%] top-12 hidden opacity-[0.05] lg:block" aria-hidden="true">
        <i class="ri-shopping-bag-3-line text-4xl text-brand-600 -rotate-12"></i>
    </div>

    <div class="pointer-events-none absolute bottom-16 right-8 opacity-[0.07] sm:right-12" aria-hidden="true">
        <svg class="h-14 w-14 rotate-12 text-accent-500" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 2C9.5 2 7.5 4 7.5 6.5c0 2.2 1.4 4.1 3.3 4.8L12 22l1.2-10.7c1.9-.7 3.3-2.6 3.3-4.8C16.5 4 14.5 2 12 2Z"/>
        </svg>
    </div>

    <div class="pointer-events-none absolute left-[8%] top-1/2 hidden -translate-y-1/2 opacity-[0.06] lg:block" aria-hidden="true">
        <svg class="h-16 w-16 -rotate-6 text-orange-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-2 3-4 5-4 8a4 4 0 0 0 8 0c0-3-2-5-4-8Z"/>
            <path stroke-linecap="round" d="M12 11v8"/>
            <path stroke-linecap="round" d="M9 14c1.5 1 4.5 1 6 0"/>
        </svg>
    </div>

    <div class="pointer-events-none absolute bottom-6 left-1/2 hidden -translate-x-1/2 opacity-[0.05] md:block" aria-hidden="true">
        <svg class="h-12 w-12 text-red-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <circle cx="12" cy="12" r="7"/>
            <path fill="#f4f7ef" d="M12 5c-1.5 0-2.5.8-2.5 2.2 0 1.8 1.2 2.8 2.5 4.8 1.3-2 2.5-3 2.5-4.8C14.5 5.8 13.5 5 12 5Z"/>
        </svg>
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-end justify-between gap-4">
            <div>
                <p class="mb-1 text-xs font-bold uppercase tracking-wider text-brand-600">Trending</p>
                <h2 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">Popular Products</h2>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex shrink-0 items-center gap-1 text-sm font-semibold text-forest-600 hover:text-brand-600">
                View all
                <i class="ri-arrow-right-s-line text-lg" aria-hidden="true"></i>
            </a>
        </div>

        @if ($popularCategoryHighlights->isEmpty())
            <p class="text-gray-500">No products available yet. Check back soon!</p>
        @else
            <div class="space-y-10 sm:space-y-12">
                @foreach ($popularCategoryHighlights as $category)
                    @if ($category->products->isNotEmpty())
                        <div>
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <h3 class="truncate text-lg font-bold text-gray-900 sm:text-xl" title="{{ $category->name }}">
                                    {{ $category->name }}
                                </h3>
                                <a
                                    href="{{ route('products.index', ['category' => $category->id]) }}"
                                    class="inline-flex shrink-0 items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700"
                                >
                                    View all
                                    <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                                </a>
                            </div>

                            <div class="relative flex items-center gap-2 sm:gap-3" data-product-carousel>
                                <button
                                    type="button"
                                    data-carousel-prev
                                    aria-label="Previous product"
                                    class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-700 shadow-sm transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600 sm:flex"
                                >
                                    <i class="ri-arrow-left-s-line text-xl" aria-hidden="true"></i>
                                </button>

                                <div class="min-w-0 flex-1 overflow-hidden touch-pan-y" data-carousel-viewport>
                                    <div data-product-carousel-track class="flex transition-transform duration-300 ease-out will-change-transform">
                                        @foreach ($category->products as $product)
                                            <div data-carousel-item class="w-1/2 shrink-0 px-1.5 sm:w-1/3 lg:w-1/5">
                                                <x-product-card :product="$product" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    data-carousel-next
                                    aria-label="Next product"
                                    class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-700 shadow-sm transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600 sm:flex"
                                >
                                    <i class="ri-arrow-right-s-line text-xl" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>
