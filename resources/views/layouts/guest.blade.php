<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-site-icons />
    <title>@yield('title', 'Account') — {{ config('app.name', 'Talipapa') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|suez-one:400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gradient-to-br from-white via-avocado-50 to-avocado-200 font-sans text-gray-900 antialiased">
    <header class="border-b border-avocado-100/80 bg-white/90 backdrop-blur-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <x-talipapa-logo />
            <a href="{{ route('landing') }}" class="text-sm font-medium text-forest-600 hover:text-brand-600">
                <i class="ri-arrow-left-s-line mr-1 align-middle" aria-hidden="true"></i>
                Back to home
            </a>
        </div>
    </header>

    <main class="flex flex-1 flex-col">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot }}
        @endif
    </main>

    @livewireScripts
    @include('layouts.partials._toast')
    @stack('scripts')
</body>
</html>
