@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')
<div class="mx-auto w-full max-w-md flex-1 px-4 py-10 sm:px-6 sm:py-14">
    <div class="mb-8 text-center">
        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-brand-600">Welcome back</p>
        <h1 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">Sign in to Talipapa</h1>
        <p class="mt-2 text-sm text-gray-600">Access your orders, cart, and account.</p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
        <x-validation-errors class="mb-4" />

        @session('status')
            <x-alert type="success" title="Check your email" class="mb-4" :dismissible="false">
                {{ $value }}
            </x-alert>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div class="flex items-center justify-between gap-3">
                <label for="remember_me" class="flex items-center gap-2 text-sm text-gray-600">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    Remember me
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full rounded-lg bg-forest-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-forest-700">
                Sign in
            </button>
        </form>
    </div>

    <p class="mt-6 text-center text-sm text-gray-600">
        New to Talipapa?
        <a href="{{ route('join') }}" class="font-semibold text-brand-600 hover:text-brand-700">Create an account</a>
    </p>
</div>
@endsection
