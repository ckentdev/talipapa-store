@props([
    'faqs' => [],
])

@php
    $siteName = config('seo.site_name');
    $siteUrl = config('app.url');
    $description = config('seo.default_description');
    $logoUrl = asset(config('seo.icon'));
    $imageUrl = asset(config('seo.default_og_image'));

    $organization = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $siteName,
        'url' => $siteUrl,
        'logo' => $logoUrl,
        'description' => $description,
    ];

    if (filled(config('marketplace.contact_email'))) {
        $organization['email'] = config('marketplace.contact_email');
    }

    $website = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $siteName,
        'url' => $siteUrl,
        'description' => $description,
        'publisher' => [
            '@type' => 'Organization',
            'name' => $siteName,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $logoUrl,
            ],
        ],
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => route('products.index').'?q={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    $webPage = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => "Fresh Grocery Delivery from Local Stores | {$siteName}",
        'url' => route('landing'),
        'description' => $description,
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => $siteUrl,
        ],
        'primaryImageOfPage' => [
            '@type' => 'ImageObject',
            'url' => $imageUrl,
        ],
    ];

    $faqPage = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($faqs)->map(fn (array $faq) => [
            '@type' => 'Question',
            'name' => $faq['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['answer'],
            ],
        ])->values()->all(),
    ];
@endphp

<script type="application/ld+json">{!! json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($website, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($webPage, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@if ($faqs !== [])
    <script type="application/ld+json">{!! json_encode($faqPage, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
