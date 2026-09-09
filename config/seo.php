<?php

return [
    'site_name' => env('SEO_SITE_NAME', env('APP_NAME', 'Talipapa')),

    'default_description' => env(
        'SEO_DEFAULT_DESCRIPTION',
        'Shop fresh groceries and everyday essentials from trusted local talipapa stores. Browse products, order online, and track delivery to your door across the Philippines.'
    ),

    'default_og_image' => env('SEO_OG_IMAGE', 'img/market.png'),

    'icon' => env('SEO_ICON', 'img/market.png'),

    'twitter_handle' => env('SEO_TWITTER_HANDLE'),

    'locale' => env('SEO_LOCALE', 'en_PH'),
];
