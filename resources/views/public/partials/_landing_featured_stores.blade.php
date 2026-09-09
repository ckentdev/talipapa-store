<section class="relative overflow-hidden bg-avocado-50/30 py-10 sm:py-12">
    {{-- Base matches Popular Products; photo masked so top/bottom reveal this color --}}
    <div
        class="pointer-events-none absolute inset-0 bg-cover bg-center bg-no-repeat opacity-[0.88]"
        style="
            background-image: url('{{ asset('img/stores.jpg') }}');
            -webkit-mask-image: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.4) 10%, rgba(0,0,0,0.92) 22%, black 32%, black 68%, rgba(0,0,0,0.92) 78%, rgba(0,0,0,0.4) 90%, transparent 100%);
            mask-image: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.4) 10%, rgba(0,0,0,0.92) 22%, black 32%, black 68%, rgba(0,0,0,0.92) 78%, rgba(0,0,0,0.4) 90%, transparent 100%);
        "
        aria-hidden="true"
    ></div>

    {{-- Bottom fade into FAQ white section --}}
    <div
        class="pointer-events-none absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-b from-transparent via-white/40 to-white"
        aria-hidden="true"
    ></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-end justify-between gap-4">
            <div>
                <p class="mb-1 text-xs font-bold uppercase tracking-wider text-brand-600">Local favorites</p>
                <h2 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">Featured Stores</h2>
            </div>
            <a href="{{ route('stores.index') }}" class="inline-flex shrink-0 items-center gap-1 text-sm font-semibold text-forest-600 hover:text-brand-600">
                View all
                <i class="ri-arrow-right-s-line text-lg" aria-hidden="true"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($featuredStores as $store)
                <x-store-card :store="$store" />
            @empty
                <p class="col-span-full text-gray-500">No stores available yet.</p>
            @endforelse
        </div>
    </div>
</section>
