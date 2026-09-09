@extends('layouts.guest')

@section('title', 'Join Talipapa')

@section('content')
<div class="mx-auto w-full max-w-4xl flex-1 px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
    <div class="mb-10 text-center">
        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-brand-600">Get started</p>
        <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Join Talipapa</h1>
        <p class="mx-auto mt-3 max-w-xl text-sm text-gray-600 sm:text-base">
            Choose how you want to use the marketplace — shop, sell, or deliver.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        <a
            href="{{ route('register') }}"
            class="group flex flex-col rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-brand-300 hover:shadow-md"
        >
            <span class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                <i class="ri-shopping-bag-3-line text-2xl" aria-hidden="true"></i>
            </span>
            <h2 class="text-lg font-bold text-gray-900 group-hover:text-brand-700">Customer</h2>
            <p class="mt-2 flex-1 text-sm leading-relaxed text-gray-600">
                Create a free account to order groceries from local stores and track deliveries.
            </p>
            <span class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-brand-600">
                Sign up to shop
                <i class="ri-arrow-right-s-line text-lg" aria-hidden="true"></i>
            </span>
        </a>

        <a
            href="{{ route('register.store') }}"
            class="group flex flex-col rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-brand-300 hover:shadow-md"
        >
            <span class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-forest-600/10 text-forest-600">
                <i class="ri-store-2-line text-2xl" aria-hidden="true"></i>
            </span>
            <h2 class="text-lg font-bold text-gray-900 group-hover:text-brand-700">Store owner</h2>
            <p class="mt-2 flex-1 text-sm leading-relaxed text-gray-600">
                Register your store, upload requirements, and start selling after admin approval.
            </p>
            <span class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-brand-600">
                Register your store
                <i class="ri-arrow-right-s-line text-lg" aria-hidden="true"></i>
            </span>
        </a>

        <a
            href="{{ route('register.rider') }}"
            class="group flex flex-col rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-brand-300 hover:shadow-md"
        >
            <span class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-accent-500/15 text-accent-600">
                <i class="ri-e-bike-2-line text-2xl" aria-hidden="true"></i>
            </span>
            <h2 class="text-lg font-bold text-gray-900 group-hover:text-brand-700">Rider</h2>
            <p class="mt-2 flex-1 text-sm leading-relaxed text-gray-600">
                Apply as a delivery partner, submit documents, and earn from neighborhood orders.
            </p>
            <span class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-brand-600">
                Apply as rider
                <i class="ri-arrow-right-s-line text-lg" aria-hidden="true"></i>
            </span>
        </a>
    </div>

    <p class="mt-10 text-center text-sm text-gray-600">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Sign in</a>
    </p>
</div>
@endsection
