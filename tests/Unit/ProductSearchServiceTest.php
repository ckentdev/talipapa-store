<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Services\Voc\QueryAttributeExtractor;
use App\Services\VoiceAssistant\ProductSearchService;
use Database\Seeders\PsgcSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductSearchService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PsgcSeeder::class);
        $this->service = app(ProductSearchService::class);
    }

    public function test_unit_match_finds_coke_one_liter(): void
    {
        $store = StoreProfile::factory()->approved()->create();
        $category = Category::factory()->create();

        Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Coke 1L',
            'description' => 'Carbonated drink',
            'is_available' => true,
            'stock' => 10,
        ]);

        Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Coke 500ml',
            'is_available' => true,
            'stock' => 10,
        ]);

        $results = $this->service->searchFromNlp([
            'original_text' => '1 liter coke',
            'keywords' => ['coke', 'coca cola', 'soda'],
            'units' => ['1L', '1000ml', '1 liter'],
        ]);

        $this->assertNotEmpty($results);
        $this->assertSame('Coke 1L', $results->first()->name);
    }

    public function test_synonym_bugas_finds_rice_product(): void
    {
        $store = StoreProfile::factory()->approved()->create();
        $category = Category::factory()->create(['name' => 'Grains']);

        Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Premium Rice 5kg',
            'is_available' => true,
            'stock' => 5,
        ]);

        $results = $this->service->searchFromNlp([
            'original_text' => 'palit kog bugas',
            'keywords' => $this->service->expandTerms(['bugas']),
            'units' => [],
        ]);

        $this->assertTrue($results->contains(fn (Product $p) => str_contains(strtolower($p->name), 'rice')));
    }

    public function test_normalize_units_from_litro(): void
    {
        $units = $this->service->normalizeUnitsFromText('1 litro nga coke');

        $this->assertContains('1L', $units);
    }

    public function test_cheap_tuyo_ranks_lower_priced_dried_fish(): void
    {
        $store = StoreProfile::factory()->approved()->create();
        $category = Category::factory()->create(['name' => 'Meat & Seafood']);
        $extractor = app(QueryAttributeExtractor::class);

        Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Premium Tuyo 250g',
            'description' => 'Large dried fish',
            'price' => 95,
            'is_available' => true,
            'stock' => 10,
        ]);

        Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Tuyo (Dried Fish) 100g',
            'description' => 'Budget dried herring',
            'price' => 35,
            'is_available' => true,
            'stock' => 10,
        ]);

        $nlp = $extractor->extract('barato na tuyo');
        $results = $this->service->searchFromNlp($nlp);

        $this->assertNotEmpty($results);
        $this->assertSame('Tuyo (Dried Fish) 100g', $results->first()->name);
    }

    public function test_non_cow_milk_excludes_fresh_cow_milk(): void
    {
        $store = StoreProfile::factory()->approved()->create();
        $category = Category::factory()->create(['name' => 'Dairy']);
        $extractor = app(QueryAttributeExtractor::class);

        Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Fresh Milk 1L',
            'description' => 'Full-cream fresh cow milk.',
            'price' => 98,
            'is_available' => true,
            'stock' => 10,
        ]);

        Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Nestle Vegan Oat Milk 300ml',
            'description' => 'Plant-based vegan oat milk. Dairy-free, not from cow.',
            'price' => 89,
            'is_available' => true,
            'stock' => 10,
        ]);

        $nlp = $extractor->extract('gatas na dili sa baka');
        $results = $this->service->searchFromNlp($nlp);
        $names = $results->pluck('name');

        $this->assertTrue($names->contains('Nestle Vegan Oat Milk 300ml'));
        $this->assertFalse($names->contains('Fresh Milk 1L'));
    }

    public function test_vegan_nestle_300ml_ranks_plant_milk_over_cow_milk(): void
    {
        $store = StoreProfile::factory()->approved()->create();
        $category = Category::factory()->create(['name' => 'Dairy']);
        $extractor = app(QueryAttributeExtractor::class);

        Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Fresh Milk 1L',
            'description' => 'Full-cream fresh cow milk.',
            'price' => 98,
            'is_available' => true,
            'stock' => 10,
        ]);

        Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Nestle Vegan Oat Milk 300ml',
            'description' => 'Plant-based vegan oat milk. Dairy-free, not from cow.',
            'price' => 89,
            'is_available' => true,
            'stock' => 10,
        ]);

        $nlp = $extractor->extract('gatas na vegan 300 ml nestle');
        $results = $this->service->searchFromNlp($nlp);

        $this->assertNotEmpty($results);
        $this->assertSame('Nestle Vegan Oat Milk 300ml', $results->first()->name);
        $this->assertFalse($results->pluck('name')->contains('Fresh Milk 1L'));
    }
}
