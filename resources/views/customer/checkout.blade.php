@extends('layouts.marketplace')

@section('voice_context_attrs')
data-voice-context="checkout" data-checkout-step="{{ $step }}"
@endsection

@section('title', 'Checkout')

@section('content')
@php
    $stepMeta = [
        1 => ['icon' => 'ri-shopping-cart-2-line', 'title' => 'Review Your Cart', 'subtitle' => 'Check items and quantities before continuing.'],
        2 => ['icon' => 'ri-user-3-line', 'title' => 'Contact Information', 'subtitle' => 'How we can reach you about this order.'],
        3 => ['icon' => 'ri-map-pin-2-line', 'title' => 'Delivery Address', 'subtitle' => 'Choose where your order should be delivered.'],
        4 => ['icon' => 'ri-bank-card-line', 'title' => 'Payment Method', 'subtitle' => 'Select how you would like to pay.'],
        5 => ['icon' => 'ri-checkbox-circle-line', 'title' => 'Confirm Your Order', 'subtitle' => 'Review everything one last time before placing your order.'],
    ];
    $current = $stepMeta[$step];
    $inputClass = 'block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-base placeholder:text-gray-400 focus:border-brand-600 focus:ring-brand-600';
@endphp

<div class="mx-auto max-w-6xl">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a
                href="{{ route('cart.index') }}"
                class="mb-2 inline-flex items-center gap-1 text-base font-medium text-gray-500 hover:text-brand-600"
            >
                <i class="ri-arrow-left-s-line text-base" aria-hidden="true"></i>
                Back to cart
            </a>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Checkout</h1>
            <p class="mt-1 text-base text-gray-600">Complete all steps to place your order.</p>
        </div>
        <span class="inline-flex w-fit items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-base text-gray-600 shadow-sm">
            <i class="ri-shopping-bag-3-line text-brand-600" aria-hidden="true"></i>
            {{ $cart->items->sum('quantity') }} {{ str('item')->plural($cart->items->sum('quantity')) }} in cart
        </span>
    </div>

    <x-customer.checkout.stepper :step="$step" />

    @if ($errors->any())
        <x-alert type="error" title="Please fix the following" class="mb-6">
            <ul class="list-inside list-disc space-y-1 text-base">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <div class="grid items-start gap-6 lg:grid-cols-3 lg:gap-8">
        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-6 py-5 sm:px-8">
                    <div class="flex items-start gap-4">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                            <i class="{{ $current['icon'] }} text-xl" aria-hidden="true"></i>
                        </span>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 sm:text-xl">{{ $current['title'] }}</h2>
                            <p class="mt-0.5 text-base text-gray-600">{{ $current['subtitle'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    @if ($step === 1)
                        @include('customer.checkout._step-cart')
                    @elseif ($step === 2)
                        @include('customer.checkout._step-info', compact('checkout', 'inputClass'))
                    @elseif ($step === 3)
                        @include('customer.checkout._step-address', compact('checkout', 'addresses'))
                    @elseif ($step === 4)
                        @include('customer.checkout._payment-step', compact('checkout', 'inputClass'))
                    @elseif ($step === 5)
                        @include('customer.checkout._step-confirm', compact('checkout', 'addresses', 'cart', 'deliveryFee'))
                    @endif
                </div>
            </div>
        </div>

        <x-customer.checkout.summary
            :cart="$cart"
            :checkout="$checkout"
            :addresses="$addresses"
            :delivery-fee="$deliveryFee"
            :step="$step"
        />
    </div>
</div>
@endsection
