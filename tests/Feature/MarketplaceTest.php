<?php

namespace Tests\Feature;

use App\Enums\ApprovalStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PsgcSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    public function test_landing_page_loads(): void
    {
        $this->get(route('landing'))->assertOk();
    }

    public function test_products_page_loads(): void
    {
        $this->get(route('products.index'))->assertOk();
    }

    public function test_psgc_provinces_endpoint(): void
    {
        $this->getJson('/psgc/provinces/130000000')
            ->assertOk()
            ->assertJsonStructure([['code', 'name']]);
    }

    public function test_customer_cannot_access_store_dashboard(): void
    {
        $user = User::factory()->create(['role' => UserRole::Customer]);

        $this->actingAs($user)
            ->get(route('store.dashboard'))
            ->assertForbidden();
    }

    public function test_unapproved_store_cannot_create_products(): void
    {
        $user = User::factory()->create(['role' => UserRole::StoreOwner]);
        StoreProfile::factory()->create([
            'user_id' => $user->id,
            'status' => ApprovalStatus::Pending,
        ]);

        $this->actingAs($user)
            ->get(route('store.products.create'))
            ->assertRedirect(route('store.dashboard'));
    }

    public function test_guest_can_add_to_cart(): void
    {
        $store = StoreProfile::factory()->approved()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
        ]);

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }
}
