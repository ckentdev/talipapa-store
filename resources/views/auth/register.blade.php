@extends('layouts.guest')

@section('title', 'Create account')

@section('content')
<div class="mx-auto w-full max-w-md flex-1 px-4 py-10 sm:px-6 sm:py-14">
    <div class="mb-8 text-center">
        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-brand-600">Customer</p>
        <h1 class="text-2xl font-extrabold text-gray-900 sm:text-3xl">Create your account</h1>
        <p class="mt-2 text-sm text-gray-600">Shop from local stores and get groceries delivered.</p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Full name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div>
                <label for="phone" class="mb-1 block text-sm font-medium text-gray-700">Phone <span class="font-normal text-gray-400">(optional)</span></label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <label for="terms" class="flex items-start gap-3 text-sm text-gray-600">
                    <input type="checkbox" name="terms" id="terms" required class="mt-1 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <span>
                        {!! __('I agree to the :terms_of_service and :privacy_policy', [
                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="font-medium text-brand-600 hover:text-brand-700">'.__('Terms of Service').'</a>',
                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="font-medium text-brand-600 hover:text-brand-700">'.__('Privacy Policy').'</a>',
                        ]) !!}
                    </span>
                </label>
            @endif

            <button type="submit" class="w-full rounded-lg bg-forest-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-forest-700">
                Create account
            </button>
        </form>
    </div>

    <p class="mt-6 text-center text-sm text-gray-600">
        Want to sell or deliver?
        <a href="{{ route('join') }}" class="font-semibold text-brand-600 hover:text-brand-700">See all registration options</a>
    </p>

    <p class="mt-3 text-center text-sm text-gray-600">
        Already registered?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Sign in</a>
    </p>
</div>
@endsection
