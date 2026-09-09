@php
    $iconUrl = asset(config('seo.icon'));
@endphp

<link rel="icon" href="{{ $iconUrl }}" type="image/png" sizes="48x48">
<link rel="apple-touch-icon" href="{{ $iconUrl }}">
