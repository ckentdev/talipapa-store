@php
    use App\Support\Breadcrumbs;

    $pageTitle = trim((string) view()->yieldContent('title'));
    $breadcrumbItems = View::hasSection('breadcrumb_items')
        ? json_decode(trim((string) view()->yieldContent('breadcrumb_items')), true) ?? []
        : Breadcrumbs::resolve($pageTitle);
@endphp

<header class="sticky top-0 z-40 border-b border-avocado-100/80 bg-white/95 shadow-sm shadow-forest-600/5 backdrop-blur-md">
    <div class="flex h-16 items-center justify-between gap-3 px-4 sm:gap-4 sm:px-8">
        <div class="flex min-w-0 flex-1 items-center gap-3">
            @if (View::hasSection('sidebar'))
                <button
                    type="button"
                    data-drawer-target="talipapa-sidebar"
                    data-drawer-toggle="talipapa-sidebar"
                    aria-controls="talipapa-sidebar"
                    class="inline-flex items-center rounded-lg p-2 text-base text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-forest-200 lg:hidden"
                >
                    <span class="sr-only">Open sidebar</span>
                    <i class="ri-menu-line text-xl" aria-hidden="true"></i>
                </button>
            @endif

            <div class="min-w-0 flex-1">
                @hasSection('breadcrumbs')
                    @yield('breadcrumbs')
                @else
                    <x-breadcrumbs :items="$breadcrumbItems" />
                @endif
            </div>
        </div>

        @auth
            <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                @include('layouts.partials._notification_bell')
                <x-header-profile-dropdown variant="app" />
            </div>
        @endauth
    </div>
</header>
