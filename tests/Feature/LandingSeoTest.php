<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StoreProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_has_crawlable_seo_tags(): void
    {
        config(['seo.site_name' => 'Talipapa']);

        $response = $this->get(route('landing'));

        $response->assertOk();
        $response->assertSee('<title>Fresh Grocery Delivery from Local Stores | Talipapa</title>', false);
        $response->assertSee('<meta name="description" content="Shop fresh groceries and everyday essentials from trusted neighborhood talipapa stores online.', false);
        $response->assertSee('<meta name="robots" content="index, follow">', false);
        $response->assertSee('<link rel="canonical" href="'.route('landing').'">', false);
        $response->assertSee('<meta property="og:title" content="Fresh Grocery Delivery from Local Stores">', false);
        $response->assertSee('<meta property="og:type" content="website">', false);
        $response->assertSee('<meta property="og:image" content="'.asset('img/market.png').'">', false);
        $response->assertSee('<link rel="icon" href="'.asset('img/market.png').'"', false);
        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('"@type":"FAQPage"', false);
        $response->assertSee('<main id="main-content"', false);
        $response->assertSee('<h1', false);
    }

    public function test_robots_txt_allows_public_pages_and_lists_sitemap(): void
    {
        $response = $this->get(route('robots'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('Allow: /');
        $response->assertSee('Disallow: /admin');
        $response->assertSee('Disallow: /cart');
        $response->assertSee('Sitemap: '.route('sitemap'));
    }

    public function test_sitemap_includes_public_urls(): void
    {
        $store = StoreProfile::factory()->approved()->create();
        $product = Product::factory()->create([
            'store_profile_id' => $store->id,
            'is_available' => true,
            'stock' => 5,
        ]);

        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee(route('landing'), false);
        $response->assertSee(route('products.index'), false);
        $response->assertSee(route('stores.index'), false);
        $response->assertSee(route('stores.show', $store), false);
        $response->assertSee(route('products.show', $product), false);
        $response->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false);
    }
}
