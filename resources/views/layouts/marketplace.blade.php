<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-site-icons />
    @hasSection('seo')
        @yield('seo')
    @else
        <x-seo-meta
            :title="trim((string) view()->yieldContent('title')) ?: null"
            :robots="View::hasSection('sidebar') ? 'noindex, nofollow' : 'index, follow'"
        />
    @endif
    @stack('structured-data')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|suez-one:400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    <script>
        window.VAPID_PUBLIC_KEY = @json(config('webpush.vapid.public_key', env('VAPID_PUBLIC_KEY')));
        window.voiceSearchEnabled = @json(config('voice-assistant.search_enabled') && filled(config('voice-assistant.openai_api_key')));
        window.voiceAssistantEnabled = @json(config('voice-assistant.enabled') && filled(config('voice-assistant.openai_api_key')));
        window.productsSearchUrl = @json(route('products.index'));
    </script>
</head>
<body @class([
    'font-sans antialiased text-gray-900',
    'bg-gray-50' => ! View::hasSection('page_background'),
]) @auth data-user-id="{{ auth()->id() }}" data-sound-enabled="{{ auth()->user()->sound_alerts_enabled ? '1' : '0' }}" data-mic-enabled="{{ auth()->user()->microphone_permission ? '1' : '0' }}" @else data-sound-enabled="0" data-mic-enabled="0" @endauth @yield('voice_context_attrs')>
    @if (View::hasSection('sidebar'))
        {{-- Role panel layout (Flowbite sidebar) --}}
        @include('layouts.partials._flowbite_sidebar')

        <div class="flex min-h-screen flex-col lg:ml-64">
            <div @class([
                'flex min-h-screen flex-1 flex-col lg:pb-0',
                'pb-[calc(5rem+env(safe-area-inset-bottom,0px))]' => View::hasSection('mobile_nav'),
            ])>
                @include('layouts.partials._app_header')

                <div class="px-4 pt-4 sm:px-8">
                    @include('layouts.partials._flash_alerts')
                </div>

                <main class="flex-1 px-4 py-6 sm:px-8">
                    @yield('content')
                </main>
            </div>

            @hasSection('mobile_nav')
                @yield('mobile_nav')
            @endif
        </div>

        <x-confirm-modal />
    @else
        @php
            $pageBackgroundUrl = View::hasSection('page_background')
                ? trim((string) view()->yieldContent('page_background'))
                : null;
        @endphp

        @if ($pageBackgroundUrl)
            <div
                class="pointer-events-none fixed inset-0 z-0 bg-cover bg-center bg-no-repeat"
                style="background-image: url('{{ $pageBackgroundUrl }}');"
                aria-hidden="true"
            ></div>
            <div
                class="pointer-events-none fixed inset-0 z-0 bg-gradient-to-b from-white/75 via-white/40 to-white/75"
                aria-hidden="true"
            ></div>
        @endif

        {{-- Public marketplace layout --}}
        <div @class([
            'relative z-10 flex min-h-screen flex-col lg:pb-0',
            'pb-[calc(5rem+env(safe-area-inset-bottom,0px))]' => ! View::hasSection('sidebar'),
        ])>
        @include('layouts.partials._marketplace_header')

        <x-voice-search-permission-modal />
        <x-voice-search-listening-modal />
        <x-voice-assistant.fab />
        <x-voice-assistant.panel />

        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            @include('layouts.partials._flash_alerts')
        </div>

        @hasSection('hero')
            <main id="main-content" class="flex-1">
                @yield('hero')

                @hasSection('after_hero')
                    @yield('after_hero')
                @endif
            </main>
        @elseif (View::hasSection('after_hero'))
            <main id="main-content" class="flex-1">
                @yield('after_hero')
            </main>
        @endif

        @hasSection('content')
            <main @class([
                'mx-auto w-full flex-1',
                'max-w-7xl px-4 py-8 sm:px-6 lg:px-8' => ! View::hasSection('hero'),
                'max-w-7xl px-4 pt-6 pb-10 sm:px-6 lg:px-8' => View::hasSection('hero') && View::hasSection('after_hero'),
                'max-w-7xl px-4 py-10 sm:px-6 lg:px-8' => View::hasSection('hero') && ! View::hasSection('after_hero'),
            ])>
                @yield('content')
            </main>
        @endif

        @include('layouts.partials._marketplace_footer')

        @hasSection('mobile_nav')
            @yield('mobile_nav')
        @elseif (! View::hasSection('sidebar'))
            @include('layouts.partials._public_mobile_nav')
        @endif
        </div>
    @endif

    @include('layouts.partials._toast')
    @livewireScripts
    @stack('scripts')
</body>
</html>
