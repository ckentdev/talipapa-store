<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'store_profile_id' => StoreProfile::factory(),
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 500),
            'stock' => fake()->numberBetween(5, 100),
            'image_path' => 'img/placeholders/product.svg',
            'is_available' => true,
        ];
    }
}
