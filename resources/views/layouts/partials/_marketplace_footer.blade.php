@php
    $contactEmail = config('marketplace.contact_email');
    $socialLinks = collect([
        'facebook' => ['url' => config('marketplace.social.facebook'), 'icon' => 'ri-facebook-circle-line', 'label' => 'Facebook'],
        'instagram' => ['url' => config('marketplace.social.instagram'), 'icon' => 'ri-instagram-line', 'label' => 'Instagram'],
        'tiktok' => ['url' => config('marketplace.social.tiktok'), 'icon' => 'ri-tiktok-line', 'label' => 'TikTok'],
        'x' => ['url' => config('marketplace.social.x'), 'icon' => 'ri-twitter-x-line', 'label' => 'X'],
        'youtube' => ['url' => config('marketplace.social.youtube'), 'icon' => 'ri-youtube-line', 'label' => 'YouTube'],
    ])->filter(fn (array $social) => filled($social['url']));
@endphp

<footer @class([
    'mt-auto border-t border-forest-700/20 bg-forest-600 text-white',
    'pb-[calc(5rem+env(safe-area-inset-bottom,0px))] lg:pb-0' => ! View::hasSection('sidebar'),
])>
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid grid-cols-1 gap-10 border-b border-white/10 pb-10 lg:grid-cols-12 lg:gap-8 lg:pb-12">
            <div class="lg:col-span-5">
                <x-talipapa-logo size="lg" textVariant="light" />
                <p class="mt-4 max-w-md text-sm leading-relaxed text-white/75 sm:text-base">
                    Your neighborhood talipapa online. Fresh groceries from trusted local stores, delivered fast to your door.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-8 sm:grid-cols-3 lg:col-span-7 lg:grid-cols-3 lg:gap-6">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white/90">Shop</h3>
                    <ul class="mt-4 space-y-3">
                        <li><a href="{{ route('products.index') }}" class="text-sm text-white/70 transition hover:text-white sm:text-base">All products</a></li>
                        <li><a href="{{ route('stores.index') }}" class="text-sm text-white/70 transition hover:text-white sm:text-base">Browse stores</a></li>
                        <li><a href="{{ route('cart.index') }}" class="text-sm text-white/70 transition hover:text-white sm:text-base">Cart</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white/90">Partners</h3>
                    <ul class="mt-4 space-y-3">
                        <li><a href="{{ route('register.store') }}" class="text-sm text-white/70 transition hover:text-white sm:text-base">Sell on Talipapa</a></li>
                        <li><a href="{{ route('register.rider') }}" class="text-sm text-white/70 transition hover:text-white sm:text-base">Become a rider</a></li>
                    </ul>
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white/90">Support</h3>
                    <ul class="mt-4 space-y-3">
                        <li><a href="{{ route('landing') }}#faq" class="text-sm text-white/70 transition hover:text-white sm:text-base">FAQ</a></li>
                        @if ($contactEmail)
                            <li>
                                <a href="mailto:{{ $contactEmail }}" class="text-sm text-white/70 transition hover:text-white sm:text-base">
                                    {{ $contactEmail }}
                                </a>
                            </li>
                        @endif
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="text-sm text-white/70 transition hover:text-white sm:text-base">Dashboard</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-sm text-white/70 transition hover:text-white sm:text-base">Sign in</a></li>
                            <li><a href="{{ route('join') }}" class="text-sm text-white/70 transition hover:text-white sm:text-base">Create account</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
        </div>

        <div @class([
            'grid grid-cols-1 items-start gap-8 border-b border-white/10 py-10 lg:gap-12 lg:py-12',
            'lg:grid-cols-2' => $socialLinks->isNotEmpty(),
        ])>
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-white/90">Stay updated</h3>
                <p class="mt-2 max-w-lg text-sm text-white/65 sm:text-base">
                    Subscribe for deals, new store openings, and delivery updates.
                </p>

                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="mt-4 max-w-xl">
                    @csrf
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <label for="newsletter-email" class="sr-only">Email address</label>
                        <input
                            id="newsletter-email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            placeholder="you@example.com"
                            class="min-w-0 flex-1 rounded-lg border border-white/15 bg-white/10 px-4 py-2.5 text-sm text-white placeholder:text-white/45 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-400/40 sm:text-base"
                        >
                        <button
                            type="submit"
                            class="inline-flex shrink-0 items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-400 sm:text-base"
                        >
                            Subscribe
                        </button>
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </form>
            </div>

            @if ($socialLinks->isNotEmpty())
                <div class="lg:text-right">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white/90">Follow us</h3>
                    <p class="mt-2 text-sm text-white/65 sm:text-base">
                        Connect with Talipapa for news, promos, and community updates.
                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-3 lg:justify-end">
                        @foreach ($socialLinks as $social)
                            <a
                                href="{{ $social['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ $social['label'] }}"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/5 text-white/80 transition hover:border-white/30 hover:bg-white/10 hover:text-white"
                            >
                                <i class="{{ $social['icon'] }} text-lg" aria-hidden="true"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-4 pt-8 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-white/60">
                &copy; {{ date('Y') }} {{ config('app.name', 'Talipapa') }}. All rights reserved.
            </p>
            <p class="text-sm text-white/50">
                Neighborhood delivery marketplace for fresh, local groceries.
            </p>
        </div>
    </div>
</footer>
