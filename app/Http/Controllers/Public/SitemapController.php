<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StoreProfile;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            $this->entry(route('landing'), now()->toAtomString(), 'daily', '1.0'),
            $this->entry(route('products.index'), now()->toAtomString(), 'hourly', '0.9'),
            $this->entry(route('stores.index'), now()->toAtomString(), 'daily', '0.9'),
        ]);

        StoreProfile::query()
            ->approved()
            ->select(['id', 'updated_at'])
            ->orderBy('id')
            ->lazy()
            ->each(function (StoreProfile $store) use ($urls) {
                $urls->push($this->entry(
                    route('stores.show', $store),
                    $store->updated_at?->toAtomString() ?? now()->toAtomString(),
                    'weekly',
                    '0.8',
                ));
            });

        Product::query()
            ->available()
            ->whereHas('store', fn ($query) => $query->approved())
            ->select(['id', 'updated_at'])
            ->orderBy('id')
            ->lazy()
            ->each(function (Product $product) use ($urls) {
                $urls->push($this->entry(
                    route('products.show', $product),
                    $product->updated_at?->toAtomString() ?? now()->toAtomString(),
                    'weekly',
                    '0.7',
                ));
            });

        return response()
            ->view('public.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * @return array{loc: string, lastmod: string, changefreq: string, priority: string}
     */
    private function entry(string $loc, string $lastmod, string $changefreq, string $priority): array
    {
        return compact('loc', 'lastmod', 'changefreq', 'priority');
    }
}
