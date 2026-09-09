<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Services\VoiceAssistant\VoiceNlpService;
use App\Services\VoiceAssistant\VoiceReplyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class VoiceAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PsgcSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);

        config([
            'voice-assistant.enabled' => true,
            'voice-assistant.openai_api_key' => 'test-key',
        ]);
    }

    public function test_process_endpoint_returns_unconfigured_when_no_api_key(): void
    {
        config(['voice-assistant.openai_api_key' => null]);

        $this->postJson(route('voice-assistant.process'), [
            'message' => 'coke',
        ])->assertStatus(503);
    }

    public function test_process_with_text_returns_products_for_guest(): void
    {
        $store = StoreProfile::factory()->approved()->create();
        $category = Category::factory()->create();

        Product::factory()->create([
            'store_profile_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Coca-Cola 1L',
            'is_available' => true,
            'stock' => 12,
        ]);

        $this->mock(VoiceNlpService::class, function ($mock) {
            $mock->shouldReceive('extract')
                ->once()
                ->andReturn([
                    'original_text' => '1 litro nga coke',
                    'language' => 'Bisaya',
                    'keywords' => ['coke', 'coca cola', 'soda'],
                    'units' => ['1L', '1000ml'],
                    'intent' => 'product_search',
                    'quantity' => 1,
                    'checkout' => null,
                ]);
        });

        $this->mock(VoiceReplyService::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->once()
                ->andReturn('Yes, I found Coca-Cola 1L available.');
        });

        $response = $this->postJson(route('voice-assistant.process'), [
            'message' => '1 litro nga coke',
            'context' => 'products',
        ]);

        $response->assertOk()
            ->assertJsonPath('intent', 'product_search')
            ->assertJsonPath('reply', 'Yes, I found Coca-Cola 1L available.')
            ->assertJsonCount(1, 'products');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
