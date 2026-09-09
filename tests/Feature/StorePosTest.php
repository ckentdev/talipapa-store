<?php

namespace Tests\Feature;

use App\Enums\ApprovalStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorePosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PsgcSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    public function test_store_owner_can_view_pos_page(): void
    {
        $user = $this->createApprovedStoreOwner();

        $this->actingAs($user)
            ->get(route('store.pos.index'))
            ->assertOk()
            ->assertSee('Point of Sales');
    }

    public function test_store_account_page_shows_pos_module(): void
    {
        $user = $this->createApprovedStoreOwner();

        $this->actingAs($user)
            ->get(route('store.account'))
            ->assertOk()
            ->assertSee('In App Point of Sales');
    }

    public function test_approved_store_can_complete_pos_sale(): void
    {
        $user = $this->createApprovedStoreOwner();
        $store = $user->storeProfile;
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'price' => 100,
            'stock' => 10,
            'is_available' => true,
        ]);

        $this->actingAs($user)
            ->post(route('store.pos.checkout'), [
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 2],
                ],
                'customer_name' => 'Maria Santos',
                'payment_method' => 'cash',
                'cash_tendered' => 500,
            ])
            ->assertRedirect(route('store.pos.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'store_profile_id' => $store->id,
            'status' => OrderStatus::Delivered->value,
            'subtotal' => 200,
            'total' => 200,
            'delivery_fee' => 0,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);
    }

    public function test_approved_store_can_complete_pos_sale_with_discount(): void
    {
        $user = $this->createApprovedStoreOwner();
        $store = $user->storeProfile;
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'price' => 200,
            'stock' => 10,
            'is_available' => true,
        ]);

        $this->actingAs($user)
            ->post(route('store.pos.checkout'), [
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1],
                ],
                'payment_method' => 'cash',
                'discount_type' => 'fixed',
                'discount_value' => 50,
                'cash_tendered' => 200,
            ])
            ->assertRedirect(route('store.pos.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'store_profile_id' => $store->id,
            'subtotal' => 200,
            'total' => 150,
        ]);
    }

    public function test_approved_store_can_complete_pos_ewallet_sale(): void
    {
        $user = $this->createApprovedStoreOwner();
        $store = $user->storeProfile;
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'price' => 50,
            'stock' => 5,
            'is_available' => true,
        ]);

        $this->actingAs($user)
            ->post(route('store.pos.checkout'), [
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1],
                ],
                'payment_method' => 'ewallet',
                'ewallet_provider' => 'gcash',
            ])
            ->assertRedirect(route('store.pos.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'store_profile_id' => $store->id,
            'payment_method' => 'ewallet',
            'notes' => "[POS] In-store sale\ne-Wallet: GCash",
        ]);
    }

    public function test_unapproved_store_cannot_checkout_pos_sale(): void
    {
        $user = User::factory()->create(['role' => UserRole::StoreOwner]);
        StoreProfile::factory()->create([
            'user_id' => $user->id,
            'status' => ApprovalStatus::Pending,
        ]);

        $this->actingAs($user)
            ->post(route('store.pos.checkout'), [
                'items' => [
                    ['product_id' => 1, 'quantity' => 1],
                ],
                'payment_method' => 'cash',
                'cash_tendered' => 100,
            ])
            ->assertRedirect(route('store.dashboard'));
    }

    private function createApprovedStoreOwner(): User
    {
        $user = User::factory()->create(['role' => UserRole::StoreOwner]);
        $store = StoreProfile::factory()->approved()->create(['user_id' => $user->id]);

        Address::query()->create([
            'user_id' => $user->id,
            'addressable_type' => StoreProfile::class,
            'addressable_id' => $store->id,
            'label' => 'Store Location',
            'region_code' => '130000000',
            'province_code' => '133900000',
            'city_code' => '133901000',
            'barangay_code' => '133901001',
            'street_address' => '123 Test Street',
            'is_default' => true,
        ]);

        return $user->fresh(['storeProfile']);
    }
}
