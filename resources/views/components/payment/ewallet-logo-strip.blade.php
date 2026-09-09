@props([
    'size' => 'md',
])

@php
    use App\Enums\EWalletProvider;

    $logoHeight = $size === 'sm' ? 'h-6' : 'h-7';
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-1.5']) }} aria-hidden="true">
    @foreach (EWalletProvider::cases() as $provider)
        <span @class([
            'inline-flex items-center justify-center overflow-hidden rounded-md bg-white px-1.5 py-1 ring-1 ring-gray-200/80',
            $size === 'sm' ? 'h-7 min-w-[2.75rem]' : 'h-8 min-w-[3rem]',
        ])>
            <img
                src="{{ $provider->logoUrl() }}"
                alt=""
                @class([$logoHeight, 'w-auto max-w-[3.5rem] object-contain'])
                loading="lazy"
            >
        </span>
    @endforeach
</div>
