@props([
    'step',
    'submitLabel' => 'Continue',
])

@php
    $backUrl = $step > 1
        ? route('customer.checkout.show', ['step' => $step - 1])
        : route('cart.index');

    $backLabel = $step > 1 ? 'Back' : 'Back to cart';
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col-reverse items-stretch gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:items-center sm:justify-between']) }}>
    <a
        href="{{ $backUrl }}"
        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-base font-medium text-gray-700 shadow-sm transition hover:bg-gray-50"
    >
        <i class="ri-arrow-left-line" aria-hidden="true"></i>
        {{ $backLabel }}
    </a>

    <button
        type="submit"
        class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-6 py-2.5 text-base font-semibold text-white shadow-sm transition hover:bg-brand-700"
    >
        @if ($submitLabel === 'Place Order')
            <i class="ri-shopping-bag-3-line" aria-hidden="true"></i>
        @endif
        {{ $submitLabel }}
        @if ($submitLabel !== 'Place Order')
            <i class="ri-arrow-right-line" aria-hidden="true"></i>
        @endif
    </button>
</div>
