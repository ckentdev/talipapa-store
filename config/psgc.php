<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PSGC API Base URL
    |--------------------------------------------------------------------------
    |
    | Philippine Standard Geographic Code API (read-only).
    | @see https://psgc.gitlab.io/api/
    |
    */

    'base_url' => env('PSGC_API_URL', 'https://psgc.gitlab.io/api'),

    'cache_ttl' => (int) env('PSGC_CACHE_TTL', 86400),

];
