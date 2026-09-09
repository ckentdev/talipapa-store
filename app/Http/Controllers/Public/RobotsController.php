<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /store',
            'Disallow: /rider',
            'Disallow: /customer',
            'Disallow: /cart',
            'Disallow: /checkout',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /join',
            'Disallow: /notifications',
            'Disallow: /voice-assistant',
            'Disallow: /psgc/',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode(PHP_EOL, $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
