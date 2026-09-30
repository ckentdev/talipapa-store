<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\RiderAvailability;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\VocSource;
use App\Models\Address;
use App\Models\Category;
use App\Models\CustomerProfile;
use App\Models\Product;
use App\Models\RiderProfile;
use App\Models\StoreProfile;
use App\Models\User;
use App\Models\VocUtterance;
use App\Services\Voc\QueryAttributeExtractor;
use App\Services\VoiceAssistant\ProductSearchService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->orderBy('id')->get()->keyBy('slug');

        $stores = [
            [
                'owner' => [
                    'email' => 'store@repomart.test',
                    'name' => 'Juan Dela Cruz',
                    'phone' => '09171234567',
                ],
                'store' => [
                    'store_name' => 'Talipapa Fresh Mart',
                    'description' => 'Your neighborhood grocery store with fresh produce and daily essentials.',
                    'cover_path' => 'img/photo-1542838132-92c53300491e.jpeg',
                ],
                'address' => [
                    'street_address' => '123 Commonwealth Avenue',
                    'barangay_code' => '137404022',
                    'landmark' => 'Near SM Fairview',
                    'postal_code' => '1109',
                    'latitude' => 14.676041,
                    'longitude' => 121.043700,
                ],
                'products' => [
                    ['name' => 'Jasmine Rice 5kg', 'description' => 'Premium long-grain jasmine rice.', 'price' => 289.00, 'category_slug' => 'grains-pasta-sides'],
                    ['name' => 'Fresh Tomatoes 1kg', 'description' => 'Locally sourced ripe tomatoes.', 'price' => 85.00, 'category_slug' => 'fruits-vegetables'],
                    ['name' => 'Pandesal (12 pcs)', 'description' => 'Freshly baked Filipino bread rolls.', 'price' => 60.00, 'category_slug' => 'bread-bakery'],
                    ['name' => 'Silver Swan Soy Sauce 200ml', 'description' => 'Everyday soy sauce. Barato na tuyo for the table.', 'price' => 28.00, 'category_slug' => 'condiments-spice-bake'],
                    ['name' => 'Premium Soy Sauce 1L', 'description' => 'Large bottle of soy sauce.', 'price' => 95.00, 'category_slug' => 'condiments-spice-bake'],
                    ['name' => 'Bulad (Dried Fish) 100g', 'description' => 'Budget dried fish.', 'price' => 35.00, 'category_slug' => 'meat-seafood'],
                ],
                'carousel_slugs' => ['grains-pasta-sides', 'fruits-vegetables'],
            ],
            [
                'owner' => [
                    'email' => 'store2@repomart.test',
                    'name' => 'Ana Reyes',
                    'phone' => '09171234568',
                ],
                'store' => [
                    'store_name' => 'Green Basket Groceries',
                    'description' => 'Organic produce, dairy, and healthy pantry staples delivered fresh.',
                    'cover_path' => 'img/hero-vendor.jpg',
                ],
                'address' => [
                    'street_address' => '88 Maginhawa Street',
                    'barangay_code' => '137404031',
                    'landmark' => 'Near UP Town Center',
                    'postal_code' => '1101',
                    'latitude' => 14.651000,
                    'longitude' => 121.075000,
                ],
                'products' => [
                    ['name' => 'Fresh Milk 1L', 'description' => 'Full-cream fresh cow milk.', 'price' => 98.00, 'category_slug' => 'dairy-eggs-cheese'],
                    ['name' => 'Nestle Vegan Oat Milk 300ml', 'description' => 'Plant-based vegan oat milk. Dairy-free, not from cow.', 'price' => 89.00, 'category_slug' => 'dairy-eggs-cheese'],
                    ['name' => 'Banana Bundle 1kg', 'description' => 'Ripe Saba bananas.', 'price' => 55.00, 'category_slug' => 'fruits-vegetables'],
                    ['name' => 'Greek Yogurt 450g', 'description' => 'Creamy plain yogurt.', 'price' => 125.00, 'category_slug' => 'dairy-eggs-cheese'],
                ],
                'carousel_slugs' => ['dairy-eggs-cheese', 'fruits-vegetables'],
            ],
            [
                'owner' => [
                    'email' => 'store3@repomart.test',
                    'name' => 'Carlo Mendoza',
                    'phone' => '09171234569',
                ],
                'store' => [
                    'store_name' => 'QuickStop Pantry',
                    'description' => 'Snacks, beverages, and convenience items for busy households.',
                    'cover_path' => 'img/photo-1542838132-92c53300491e.jpeg',
                ],
                'address' => [
                    'street_address' => '15 Timog Avenue',
                    'barangay_code' => '137404015',
                    'landmark' => 'Near Trinoma',
                    'postal_code' => '1103',
                    'latitude' => 14.635000,
                    'longitude' => 121.034000,
                ],
                'products' => [
                    ['name' => 'Bottled Water 1L (6-pack)', 'description' => 'Purified drinking water.', 'price' => 95.00, 'category_slug' => 'beverages'],
                    ['name' => 'Potato Chips 150g', 'description' => 'Classic salted potato chips.', 'price' => 65.00, 'category_slug' => 'cookies-snacks-candy'],
                    ['name' => 'Instant Noodles (5-pack)', 'description' => 'Assorted flavors.', 'price' => 75.00, 'category_slug' => 'grains-pasta-sides'],
                ],
                'carousel_slugs' => ['beverages', 'cookies-snacks-candy'],
            ],
        ];

        foreach ($stores as $storeData) {
            $this->seedStore($storeData, $categories);
        }

        $rider = User::updateOrCreate(
            ['email' => 'rider@repomart.test'],
            [
                'name' => 'Pedro Santos',
                'password' => 'password',
                'role' => UserRole::Rider,
                'phone' => '09181234567',
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]
        );

        RiderProfile::updateOrCreate(
            ['user_id' => $rider->id],
            [
                'vehicle_type' => 'motorcycle',
                'plate_number' => 'ABC 1234',
                'status' => ApprovalStatus::Approved,
                'availability' => RiderAvailability::Available,
                'approved_at' => now(),
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@repomart.test'],
            [
                'name' => 'Maria Garcia',
                'password' => 'password',
                'role' => UserRole::Customer,
                'phone' => '09191234567',
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]
        );

        CustomerProfile::updateOrCreate(
            ['user_id' => $customer->id],
            [
                'preferences' => [
                    'notifications' => true,
                    'default_payment' => 'cod',
                ],
            ]
        );

        Address::updateOrCreate(
            [
                'user_id' => $customer->id,
                'addressable_type' => null,
                'addressable_id' => null,
                'label' => 'Home',
            ],
            [
                'region_code' => '130000000',
                'province_code' => '133900000',
                'city_code' => '137401000',
                'barangay_code' => '137401038',
                'street_address' => '456 Adriatico Street',
                'postal_code' => '1004',
                'landmark' => 'Near Robinsons Place Manila',
                'latitude' => 14.563500,
                'longitude' => 120.984200,
                'is_default' => true,
            ]
        );

        $this->seedVocExamples();
    }

    /**
     * @param  array<string, mixed>  $storeData
     * @param  Collection<string, Category>  $categories
     */
    private function seedStore(array $storeData, $categories): void
    {
        $owner = User::updateOrCreate(
            ['email' => $storeData['owner']['email']],
            [
                'name' => $storeData['owner']['name'],
                'password' => 'password',
                'role' => UserRole::StoreOwner,
                'phone' => $storeData['owner']['phone'],
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]
        );

        $storeProfile = StoreProfile::updateOrCreate(
            ['user_id' => $owner->id],
            [
                'store_name' => $storeData['store']['store_name'],
                'description' => $storeData['store']['description'],
                'cover_path' => $storeData['store']['cover_path'],
                'status' => ApprovalStatus::Approved,
                'approved_at' => now(),
            ]
        );

        Address::updateOrCreate(
            [
                'user_id' => $owner->id,
                'addressable_type' => StoreProfile::class,
                'addressable_id' => $storeProfile->id,
            ],
            [
                'label' => 'Store Location',
                'region_code' => '130000000',
                'province_code' => '137600000',
                'city_code' => '137404000',
                'barangay_code' => $storeData['address']['barangay_code'],
                'street_address' => $storeData['address']['street_address'],
                'postal_code' => $storeData['address']['postal_code'],
                'landmark' => $storeData['address']['landmark'],
                'latitude' => $storeData['address']['latitude'],
                'longitude' => $storeData['address']['longitude'],
                'is_default' => true,
            ]
        );

        foreach ($storeData['products'] as $product) {
            $category = $categories[$product['category_slug']] ?? null;
            if (! $category) {
                continue;
            }

            Product::updateOrCreate(
                [
                    'store_profile_id' => $storeProfile->id,
                    'name' => $product['name'],
                ],
                [
                    'category_id' => $category->id,
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => 50,
                    'image_path' => 'img/placeholders/product.svg',
                    'is_available' => true,
                ]
            );
        }

        foreach ($storeData['carousel_slugs'] as $slug) {
            $category = $categories[$slug] ?? null;
            if (! $category) {
                continue;
            }

            for ($i = 1; $i <= 10; $i++) {
                Product::updateOrCreate(
                    [
                        'store_profile_id' => $storeProfile->id,
                        'name' => $storeProfile->store_name.' — '.ucfirst(str_replace('-', ' ', $slug)).' Item '.$i,
                    ],
                    [
                        'category_id' => $category->id,
                        'description' => 'Demo highlight product for category carousel.',
                        'price' => 49.00 + ($i * 5),
                        'stock' => 25,
                        'image_path' => 'img/placeholders/product.svg',
                        'is_available' => true,
                    ]
                );
            }
        }
    }

    private function seedVocExamples(): void
    {
        if (VocUtterance::query()->exists()) {
            return;
        }

        $extractor = app(QueryAttributeExtractor::class);
        $search = app(ProductSearchService::class);

        $phrases = [
            ['text' => 'barato na tuyo', 'times' => 5, 'source' => VocSource::Search],
            ['text' => 'gatas na dili sa baka', 'times' => 4, 'source' => VocSource::Voice],
            ['text' => 'gatas na vegan 300 ml nestle', 'times' => 6, 'source' => VocSource::Search],
            ['text' => 'organic quinoa 1kg', 'times' => 3, 'source' => VocSource::Search],
        ];

        foreach ($phrases as $row) {
            $nlp = $extractor->extract($row['text']);
            $resultCount = $search->searchFromNlp($nlp)->count();

            for ($i = 0; $i < $row['times']; $i++) {
                $utterance = VocUtterance::query()->create([
                    'user_id' => null,
                    'source' => $row['source'],
                    'original_text' => $row['text'],
                    'language' => $nlp['language'],
                    'intent' => $nlp['intent'],
                    'result_count' => $resultCount,
                ]);

                foreach ($nlp['attributes'] as $attribute) {
                    $utterance->attributes()->create($attribute);
                }
            }
        }
    }
}
