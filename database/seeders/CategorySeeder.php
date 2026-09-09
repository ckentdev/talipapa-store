<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Star Market shop-by-category aisles (display order preserved).
     *
     * @return array<int, array{name: string, image: string}>
     */
    public static function definitions(): array
    {
        return [
            ['name' => 'Buy It Again', 'image' => 'buy-it-again.png'],
            ['name' => 'Deals', 'image' => 'deals.png'],
            ['name' => 'Recipes & Meals', 'image' => 'recipes-meals.png'],
            ['name' => 'Flowers, Cards, Occasion', 'image' => 'flowers-cards-occasion.png'],
            ['name' => 'Dairy, Eggs & Cheese', 'image' => 'dairy-eggs-cheese.png'],
            ['name' => 'Beverages', 'image' => 'beverages.png'],
            ['name' => 'Frozen Foods', 'image' => 'frozen-foods.png'],
            ['name' => 'Cookies, Snacks & Candy', 'image' => 'cookies-snacks-candy.png'],
            ['name' => 'Breakfast & Cereal', 'image' => 'breakfast-cereal.png'],
            ['name' => 'Wine, Beer & Spirits', 'image' => 'wine-beer-spirits.png'],
            ['name' => 'Paper, Cleaning & Home', 'image' => 'paper-cleaning-home.png'],
            ['name' => 'Personal Care & Health', 'image' => 'personal-care-health.png'],
            ['name' => 'Pet Care', 'image' => 'pet-care.png'],
            ['name' => 'Condiments, Spice & Bake', 'image' => 'condiments-spice-bake.png'],
            ['name' => 'Grains, Pasta & Sides', 'image' => 'grains-pasta-sides.png'],
            ['name' => 'International Cuisine', 'image' => 'international-cuisine.png'],
            ['name' => 'Canned Goods & Soups', 'image' => 'canned-goods-soups.png'],
            ['name' => 'Baby Care', 'image' => 'baby-care.png'],
            ['name' => 'Meat & Seafood', 'image' => 'meat-seafood.png'],
            ['name' => 'Fruits & Vegetables', 'image' => 'fruits-vegetables.png'],
            ['name' => 'Bread & Bakery', 'image' => 'bread-bakery.png'],
            ['name' => 'Exclusive Brands', 'image' => 'exclusive-brands.png'],
            ['name' => 'Order Ahead', 'image' => 'order-ahead.png'],
            ['name' => 'Deli', 'image' => 'deli.png'],
            ['name' => 'Nutrition & Wellness', 'image' => 'nutrition-wellness.png'],
        ];
    }

    public function run(): void
    {
        $now = now();
        $rows = [];
        $activeSlugs = [];

        foreach (self::definitions() as $index => $category) {
            $slug = Str::slug($category['name']);
            $activeSlugs[] = $slug;

            $rows[] = [
                'name' => $category['name'],
                'slug' => $slug,
                'image_path' => 'img/categories/'.$category['image'],
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('categories')->upsert(
            $rows,
            ['slug'],
            ['name', 'image_path', 'is_active', 'sort_order', 'updated_at']
        );

        DB::table('categories')
            ->whereNotIn('slug', $activeSlugs)
            ->update(['is_active' => false, 'updated_at' => $now]);
    }
}
