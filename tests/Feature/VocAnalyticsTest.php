<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\VocSource;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Models\User;
use App\Models\VocUtterance;
use Database\Seeders\PsgcSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VocAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PsgcSeeder::class);
    }

    public function test_product_search_logs_utterance_and_attributes(): void
    {
        $store = StoreProfile::factory()->approved()->create();
        $category = Category::factory()->create(['name' => 'Meat & Seafood']);
        Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Silver Swan Soy Sauce 200ml',
            'description' => 'Everyday soy sauce',
            'price' => 35,
            'is_available' => true,
            'stock' => 8,
        ]);

        $this->get(route('products.index', ['q' => 'barato na tuyo']))
            ->assertOk()
            ->assertSee('Silver Swan Soy Sauce 200ml');

        $this->assertDatabaseHas('voc_utterances', [
            'original_text' => 'barato na tuyo',
            'source' => VocSource::Search->value,
        ]);

        $utterance = VocUtterance::query()->first();
        $this->assertNotNull($utterance);
        $this->assertTrue($utterance->attributes->contains(
            fn ($attribute) => $attribute->type->value === 'product' && $attribute->value === 'soy sauce'
        ));
        $this->assertTrue($utterance->attributes->contains(
            fn ($attribute) => $attribute->type->value === 'price_intent' && $attribute->value === 'cheap'
        ));
    }

    public function test_admin_can_view_voc_dashboard(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)
            ->get(route('admin.voc.index'))
            ->assertOk()
            ->assertSee('Voice of Customer')
            ->assertSee('barato na tuyo');
    }

    public function test_customer_cannot_view_voc_dashboard(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $this->actingAs($customer)
            ->get(route('admin.voc.index'))
            ->assertForbidden();
    }

    public function test_admin_playground_extracts_vegan_nestle_query(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $store = StoreProfile::factory()->approved()->create();
        $category = Category::factory()->create(['name' => 'Dairy']);
        Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Nestle Vegan Oat Milk 300ml',
            'description' => 'Plant-based vegan oat milk.',
            'price' => 89,
            'is_available' => true,
            'stock' => 10,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.voc.extract'), ['q' => 'gatas na vegan 300 ml nestle'])
            ->assertOk()
            ->assertJsonPath('product', 'milk')
            ->assertJsonPath('brand', 'nestle')
            ->assertJsonPath('price_intent', null)
            ->assertJsonFragment(['name' => 'Nestle Vegan Oat Milk 300ml']);

        $this->assertDatabaseHas('voc_utterances', [
            'original_text' => 'gatas na vegan 300 ml nestle',
            'source' => VocSource::Playground->value,
        ]);
    }
}
