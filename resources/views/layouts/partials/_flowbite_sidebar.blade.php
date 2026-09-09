<aside
    id="talipapa-sidebar"
    class="fixed top-0 left-0 z-40 h-screen w-64 -translate-x-full border-r border-gray-200 bg-white transition-transform lg:translate-x-0"
    aria-label="Sidebar"
    tabindex="-1"
>
    <div class="relative flex h-full flex-col overflow-y-auto px-3 py-4">
        <button
            type="button"
            data-drawer-hide="talipapa-sidebar"
            aria-controls="talipapa-sidebar"
            class="absolute end-2.5 top-2.5 inline-flex items-center rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-900 lg:hidden"
        >
            <span class="sr-only">Close sidebar</span>
            <i class="ri-close-line text-xl" aria-hidden="true"></i>
        </button>

        {{-- Logo branding --}}
        <x-talipapa-logo size="sm" class="mb-4 ps-2.5" />

        @auth
            <div class="mb-4 rounded-lg border border-avocado-100 bg-avocado-50/60 px-3 py-2.5">
                <p class="truncate text-base font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                <p class="truncate text-base text-gray-500">{{ auth()->user()->email }}</p>
            </div>
        @endauth

        <nav class="flex-1 space-y-4">
            @hasSection('sidebar')
                @yield('sidebar')
            @endif
        </nav>

        {{-- Sign out --}}
        <div class="mt-4 border-t border-gray-200 pt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="group flex w-full items-center rounded-lg p-2 text-base font-medium text-gray-700 transition hover:bg-gray-100"
                >
                    <i class="ri-logout-box-r-line h-5 w-5 shrink-0 text-gray-400 transition duration-75 group-hover:text-forest-600" aria-hidden="true"></i>
                    <span class="ms-3 whitespace-nowrap">Sign out</span>
                </button>
            </form>
        </div>
    </div>
</aside>
