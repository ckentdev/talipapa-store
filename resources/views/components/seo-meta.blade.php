@props([
    'title' => null,
    'description' => null,
    'canonical' => null,
    'image' => null,
    'type' => 'website',
    'robots' => 'index, follow',
])

@php
    $siteName = config('seo.site_name');
    $pageTitle = filled($title) ? $title : $siteName;
    $documentTitle = filled($title) && ! str_contains($title, $siteName)
        ? "{$pageTitle} | {$siteName}"
        : $pageTitle;
    $metaDescription = $description ?? config('seo.default_description');
    $canonicalUrl = $canonical ?? url()->current();
    $imageUrl = $image ?? asset(config('seo.default_og_image'));
    $locale = str_replace('_', '-', config('seo.locale', 'en_PH'));
@endphp

<title>{{ $documentTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:image" content="{{ $imageUrl }}">
<meta property="og:image:alt" content="{{ $siteName }} logo">
<meta name="image" content="{{ $imageUrl }}">
<meta property="og:locale" content="{{ $locale }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $imageUrl }}">
@if (filled(config('seo.twitter_handle')))
    <meta name="twitter:site" content="{{ config('seo.twitter_handle') }}">
@endif
