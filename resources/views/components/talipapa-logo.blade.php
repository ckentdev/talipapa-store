@props(['showText' => true, 'size' => 'md', 'textVariant' => 'default'])

@php
    $textSize = match ($size) {
        'sm' => 'text-xl',
        'lg' => 'text-4xl',
        default => 'text-3xl',
    };
    $iconSize = match ($size) {
        'sm' => 'h-9 w-9',
        'lg' => 'h-14 w-14',
        default => 'h-12 w-12',
    };
    $gap = match ($size) {
        'sm' => 'gap-2',
        'lg' => 'gap-3.5',
        default => 'gap-3',
    };

    $textClass = $textVariant === 'light' ? 'text-white' : '!text-dark';
@endphp

<a {{ $attributes->merge(['href' => route('landing'), 'class' => "group inline-flex shrink-0 items-center {$gap}"]) }}>
    <img
        src="{{ asset('img/market.png') }}"
        alt=""
        class="{{ $iconSize }} shrink-0 object-contain transition-transform duration-200 group-hover:scale-105"
        width="48"
        height="48"
        loading="eager"
        decoding="async"
    >

    @if ($showText)
        <span
            data-logo-text
            class="{{ $textSize }} {{ $textClass }} font-logo leading-none tracking-normal !text-dark"
        >
            Talipapa
        </span>
    @endif
</a>
