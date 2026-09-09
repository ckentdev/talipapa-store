@props([
    'src' => null,
    'fallback' => null,
    'alt' => '',
    'type' => 'product',
])

@php
    use App\Support\ImageUrl;

    $fallbackUrl = $fallback ?? ImageUrl::fallback($type);
    $sourceUrl = filled($src) ? $src : $fallbackUrl;
@endphp

<img
    src="{{ $sourceUrl }}"
    alt="{{ $alt }}"
    onerror="this.onerror=null;this.src='{{ $fallbackUrl }}'"
    {{ $attributes }}
>
