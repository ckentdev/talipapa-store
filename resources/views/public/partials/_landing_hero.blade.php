{{-- PioMart-style hero section --}}
<section class="relative overflow-hidden bg-gradient-to-br from-white via-avocado-50 to-avocado-200">
    {{-- Decorative faint illustrations --}}
    <div class="pointer-events-none absolute left-8 top-8 hidden opacity-[0.07] lg:block" aria-hidden="true">
        <svg class="h-24 w-24 text-gray-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C9.5 2 7.5 4 7.5 6.5c0 2.2 1.4 4.1 3.3 4.8L12 22l1.2-10.7c1.9-.7 3.3-2.6 3.3-4.8C16.5 4 14.5 2 12 2Z"/></svg>
    </div>
    <div class="pointer-events-none absolute bottom-16 left-1/2 hidden -translate-x-1/2 opacity-[0.06] lg:block" aria-hidden="true">
        <svg class="h-28 w-28 text-gray-900" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M5.5 17.5h13M9 10l3-7 3 7M6 10h12"/></svg>
    </div>

    <div class="mx-auto grid max-w-7xl items-center gap-8 px-4 py-10 sm:px-6 lg:grid-cols-2 lg:gap-4 lg:px-8 lg:py-16">
        {{-- Left: copy --}}
        <div class="relative z-10 max-w-xl">
            <div class="mb-4 flex items-center gap-3">
                <span class="h-0.5 w-8 rounded bg-accent-500"></span>
                <p class="text-sm font-bold uppercase tracking-wide text-gray-800">Neighborhood Mart Online Delivery</p>
            </div>

            <h1 class="mb-5 text-4xl font-extrabold leading-tight text-gray-900 sm:text-5xl lg:text-[3.25rem] lg:leading-[1.15]">
                Make Healthy Life With <span class="text-brand-600">Fresh</span> Grocery
            </h1>

            <p class="mb-3 max-w-md text-lg font-bold leading-snug text-gray-900">
                Your neighborhood talipapa — fresh groceries, local stores, delivered to your door.
            </p>

            <p class="mb-8 max-w-md text-base leading-relaxed text-gray-500">
                Shop from trusted local stores, get farm-fresh produce delivered fast, and track every order from cart to doorstep.
            </p>

            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2.5 rounded-full bg-accent-500 px-6 py-3.5 text-sm font-bold text-gray-900 shadow-md transition hover:bg-accent-600 hover:shadow-lg sm:px-8">
                    <i class="ri-shopping-bag-3-line shrink-0 text-lg" aria-hidden="true"></i>
                    Shop Now
                </a>

                <a href="{{ route('stores.index') }}" class="inline-flex items-center gap-2.5 rounded-full border-2 border-forest-600 bg-forest-600 px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:border-forest-700 hover:bg-forest-700 hover:shadow-lg sm:px-8">
                    <i class="ri-store-2-line shrink-0 text-lg" aria-hidden="true"></i>
                    Browse Stores
                </a>
            </div>

            {{-- Social proof pill --}}
            <div class="mt-10 inline-flex items-center gap-3 rounded-full border border-gray-200 bg-white px-4 py-2.5 shadow-sm">
                <div class="flex -space-x-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700 ring-2 ring-white"><img src="https://scontent.fceb1-5.fna.fbcdn.net/v/t39.30808-1/521533292_10236655404581894_6723908172920594008_n.jpg?stp=dst-jpg_s200x200_tt6&_nc_cat=102&ccb=1-7&_nc_sid=e99d92&_nc_eui2=AeFw27VJIzlVA1Tl9_pKKLp6U2HPsuBeCqFTYc-y4F4KoTJhTXnj6fnKr8lAuJ3sKrSL1zlUl5XQ0LBlhrm65_R2&_nc_ohc=7urrvGqjJf4Q7kNvwEisBW9&_nc_oc=AdqCHapBsfbtQoM_aQwqkZDkkzPpaQis3pFOJpLUEZDJrDUaBsfqTTzUbLmr0JEAGcc&_nc_zt=24&_nc_ht=scontent.fceb1-5.fna&_nc_gid=hBIorx6kOtKnNMitGjxpoA&_nc_ss=7b2a8&oh=00_Af78WMRG5fN0TI8Rvk4saFvFYapcpB4gF0PIBIDnr7beag&oe=6A1E8087" alt="" class="avatar rounded-full"></span>
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-accent-400 text-xs font-bold text-gray-900 ring-2 ring-white"><img src="https://scontent.fcgy2-4.fna.fbcdn.net/v/t39.30808-1/432763970_8106140816079845_1527195488354471885_n.jpg?stp=dst-jpg_s100x100_tt6&_nc_cat=108&ccb=1-7&_nc_sid=1d2534&_nc_eui2=AeGWLB9REehzRVTCC1tTOHiB7T1Ux46050ztPVTHjrTnTLTJ7t4FkrVfVj7UhRM2Xpi3ngQn28LnpNRFae91t7Lu&_nc_ohc=4P_Zx-08_hkQ7kNvwFZ4mT3&_nc_oc=AdqYlaivTnJhxHX7gZuKSN0JDtj-VUCnh8MKrvJkdDI23aiEbsKh6ks_WzknEmfKYz0&_nc_ad=z-m&_nc_cid=0&_nc_zt=24&_nc_ht=scontent.fcgy2-4.fna&_nc_gid=9THwArF0HRCTDuDDFwHd0w&_nc_ss=7a22e&oh=00_Af6MP-Yu1eR414wyPiAad4K2xTXgqxN8psUqqS9_OAdP_w&oe=6A1E4BAB" alt="" class="avatar rounded-full"></span>
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-forest-600 text-xs font-bold text-white ring-2 ring-white"><img src="https://scontent.fcgy2-4.fna.fbcdn.net/v/t39.30808-1/464810437_9209292832418079_5196480378871007607_n.jpg?stp=dst-jpg_s100x100_tt6&_nc_cat=108&ccb=1-7&_nc_sid=1d2534&_nc_eui2=AeG0b9E0dJhAvsV-UpcO0ZK18n-Ojw8X5J3yf46PDxfknbxTi--6r6796jcJ-tA5rsrqH9SqKRl6mmvAOuFB7JIo&_nc_ohc=LOkGv2t8vKIQ7kNvwGXX153&_nc_oc=AdpAn8FH_GUlnlL5Q9QyNOvpgH6H96WDIJNtAC7oJITKogf1eARscAGHR2Fuv2ghmz8&_nc_ad=z-m&_nc_cid=0&_nc_zt=24&_nc_ht=scontent.fcgy2-4.fna&_nc_gid=JnLsU2eocgIOaZ0h9UzIXw&_nc_ss=7a22e&oh=00_Af5kPmwzwNGRO8WWCjXC-7OpsFDpZb9c39kdVaafdGYung&oe=6A1E2B54" alt="" class="avatar rounded-full"></span>
                </div>
                <p class="text-xs font-medium text-gray-600 sm:text-sm">
                    <span class="font-bold text-gray-900">Talipapa</span> — Satisfied customers across the Philippines
                </p>
            </div>
        </div>

        {{-- Right: visual --}}
        <div class="relative mx-auto w-full max-w-lg lg:max-w-none lg:justify-self-end">
            {{-- Green diagonal backdrop --}}
            <div class="absolute inset-y-4 right-0 w-[88%] skew-x-[-6deg] rounded-3xl bg-forest-700 shadow-xl" aria-hidden="true"></div>

            {{-- Vertical GROCERY text --}}
            <p class="pointer-events-none absolute right-2 top-1/2 z-10 hidden -translate-y-1/2 select-none text-5xl font-black uppercase tracking-widest text-sage-300/40 [writing-mode:vertical-rl] lg:block" aria-hidden="true">Grocery</p>

            {{-- Vendor image --}}
            <div class="relative z-20 flex justify-center lg:justify-end">
                <x-img
                    src="{{ asset('img/photo-1542838132-92c53300491e.jpeg') }}"
                    alt="Fresh grocery vendor"
                    type="hero"
                    class="relative max-h-[420px] w-auto max-w-full object-contain drop-shadow-2xl rounded-3xl"
                />
            </div>

            {{-- Fast delivery floating card --}}
            <div class="absolute bottom-20 left-0 z-30 -translate-x-3 sm:-translate-x-4 lg:bottom-24 lg:-translate-x-8">
                <div class="relative -rotate-2 drop-shadow-xl">
                    {{-- Accent glow --}}
                    <div class="absolute -inset-0.5 rounded-xl bg-gradient-to-r from-accent-400 to-brand-500 opacity-35 blur-sm" aria-hidden="true"></div>

                    <div class="relative overflow-hidden rounded-xl border border-accent-300/80 bg-white shadow-lg">
                        {{-- Top highlight strip --}}
                        <div class="h-0.5 bg-gradient-to-r from-accent-400 via-accent-500 to-brand-500" aria-hidden="true"></div>

                        <div class="flex items-center gap-2.5 px-3 py-2.5 sm:gap-3 sm:px-3.5 sm:py-3">
                            {{-- Icon --}}
                            <div class="relative shrink-0">
                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-accent-400 to-accent-600 shadow-md ring-2 ring-accent-100">
                                    <i class="ri-truck-line text-lg text-forest-800" aria-hidden="true"></i>
                                </span>
                                <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-brand-600 text-[8px] shadow ring-2 ring-white" aria-hidden="true">⚡</span>
                            </div>

                            <div class="min-w-0">
                                <div class="mb-0.5 flex items-center gap-1.5">
                                    <p class="text-sm font-extrabold text-gray-900">
                                        <span class="text-brand-600">Fast</span> Delivery
                                    </p>
                                    <span class="rounded-full bg-brand-50 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-brand-700 ring-1 ring-brand-200">
                                        Live
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5">
                                    <div class="flex items-center gap-0.5">
                                        @foreach (range(1, 5) as $star)
                                            <i @class(['text-xs', $star <= 4 ? 'ri-star-fill text-accent-500' : 'ri-star-line text-gray-200'])" aria-hidden="true"></i>
                                        @endforeach
                                    </div>
                                    <p class="text-xs font-bold text-gray-900">4.8</p>
                                    <p class="text-[10px] text-gray-500">(10k+ reviews)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Natural products seal --}}
            <div class="absolute -bottom-2 right-0 z-30 hidden sm:block sm:right-2 lg:-bottom-11 lg:right-5">
                <div class="relative h-[6.5rem] w-[6.5rem] rotate-6 drop-shadow-xl">
                    {{-- Outer stamp ring --}}
                    <div class="absolute inset-0 rounded-full bg-gradient-to-br from-accent-400 to-accent-600 p-[3px] shadow-lg">
                        <div class="relative h-full w-full rounded-full bg-white p-[3px]">
                            <div class="relative flex h-full w-full items-center justify-center rounded-full border-2 border-dashed border-accent-400/70 bg-gradient-to-b from-white to-brand-50/40">
                                {{-- Curved label --}}
                                <svg class="absolute inset-0 h-full w-full" viewBox="0 0 120 120" aria-hidden="true">
                                    <defs>
                                        <path id="naturalBadgeArc" d="M 60,60 m -44,0 a 44,44 0 1,1 88,0 a 44,44 0 1,1 -88,0"/>
                                    </defs>
                                    <text fill="#1B4332" font-size="8.5" font-weight="800" letter-spacing="2.2">
                                        <textPath href="#naturalBadgeArc" startOffset="50%" text-anchor="middle">
                                            100% NATURAL PRODUCTS • TALIPAPA FRESH •
                                        </textPath>
                                    </text>
                                </svg>

                                {{-- Center icon --}}
                                <div class="relative z-10 flex flex-col items-center justify-center text-center">
                                    <svg class="h-7 w-7" viewBox="0 0 32 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M16 4c-2 0-4 2-5 4-1-2-3-3-5-2 2 1 3 3 3 5 0-2 1-4 3-5 2 1 4 3 5 5 1-2 3-3 5-3-2-1-3-3-3-5 1 2 3 4 5 4Z" fill="#22c55e"/>
                                        <path d="M16 14c-5 4-8 14-7 24 0 4 3 7 7 7s7-3 7-7c1-10-2-20-7-24Z" fill="#F97316"/>
                                    </svg>
                                    <span class="mt-0.5 text-[10px] font-black leading-none text-forest-700">100%</span>
                                    <span class="mt-0.5 text-[7px] font-bold uppercase leading-tight tracking-wide text-brand-700">Natural</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ribbon tag --}}
                    <span class="absolute -bottom-1 left-1/2 z-20 -translate-x-1/2 whitespace-nowrap rounded-full bg-forest-700 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider text-white shadow-md">
                        Farm Fresh
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
