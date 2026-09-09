@props([
    'label',
    'for' => null,
    'error' => null,
    'errorBag' => null,
])

@php
    $inputClass = 'block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-base placeholder:text-gray-400 focus:border-brand-600 focus:ring-brand-600';
    $labelClass = 'mb-1 block text-base font-medium text-gray-700';
@endphp

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    <label @if ($for) for="{{ $for }}" @endif class="{{ $labelClass }}">{{ $label }}</label>
    {{ $slot }}
    @if ($error)
        <x-input-error :for="$error" :bag="$errorBag" class="mt-1 text-base" />
    @endif
</div>
