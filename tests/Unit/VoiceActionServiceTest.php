<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\VoiceAssistant\VoiceActionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Tests\TestCase;

class VoiceActionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_set_payment_method_cod_in_checkout_session(): void
    {
        $user = User::factory()->create(['role' => UserRole::Customer]);
        $request = Request::create('/voice-assistant/process', 'POST');
        $request->setUserResolver(fn () => $user);
        $request->setLaravelSession($this->app['session.store']);

        $service = app(VoiceActionService::class);

        $result = $service->apply($request, [
            'intent' => 'set_payment_method',
            'checkout' => ['payment_method' => 'cod'],
        ], new Collection);

        $this->assertTrue($result['success']);
        $this->assertSame('cod', $request->session()->get('checkout.payment_method'));
    }

    public function test_set_delivery_note_in_checkout_session(): void
    {
        $user = User::factory()->create(['role' => UserRole::Customer]);
        $request = Request::create('/voice-assistant/process', 'POST');
        $request->setUserResolver(fn () => $user);
        $request->setLaravelSession($this->app['session.store']);

        $service = app(VoiceActionService::class);

        $result = $service->apply($request, [
            'intent' => 'set_delivery_note',
            'original_text' => 'Ibutang lang sa gate',
            'checkout' => ['notes' => 'Ibutang lang sa gate'],
        ], new Collection);

        $this->assertTrue($result['success']);
        $this->assertSame('Ibutang lang sa gate', $request->session()->get('checkout.notes'));
    }

    public function test_guest_cart_action_requires_login(): void
    {
        $request = Request::create('/voice-assistant/process', 'POST');
        $request->setLaravelSession($this->app['session.store']);

        $service = app(VoiceActionService::class);

        $result = $service->apply($request, [
            'intent' => 'add_to_cart',
            'checkout' => ['product_id' => 1],
        ], new Collection);

        $this->assertTrue($result['requires_login']);
    }
}
