<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_customer_registration_stepper_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Create your account');
        $response->assertSee('Account Information');
    }

    public function test_new_customers_can_register_via_stepper(): void
    {
        $this->post('/register/step/1', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '9171234567',
            'password' => 'Str0ngPass!',
            'password_confirmation' => 'Str0ngPass!',
        ])->assertRedirect(route('register', ['step' => 2]));

        $this->post('/register/step/2', [
            'label' => 'Home',
            'region_code' => '130000000',
            'province_code' => '137400000',
            'city_code' => '137404000',
            'barangay_code' => '137404031',
            'street_address' => '123 Test Street',
            'postal_code' => '1101',
            'landmark' => 'Near park',
        ])->assertRedirect(route('register', ['step' => 3]));

        $this->post('/register/step/3', [
            'delivery_instructions' => 'Leave at gate',
            'contact_preferences' => ['phone', 'sms'],
            'marketing_opt_in' => '1',
        ])->assertRedirect(route('register', ['step' => 4]));

        $response = $this->post('/register/submit');

        $this->assertAuthenticated();
        $response->assertRedirect(route('landing', absolute: false));

        $user = User::query()->where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Test User', $user->name);
        $this->assertSame('+639171234567', $user->phone);

        $profile = CustomerProfile::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertSame('Leave at gate', $profile->preferences['delivery_instructions']);
        $this->assertSame(['phone', 'sms'], $profile->preferences['contact_preferences']);
        $this->assertTrue($profile->preferences['marketing_opt_in']);

        $address = Address::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($address);
        $this->assertTrue($address->is_default);
        $this->assertSame('123 Test Street', $address->street_address);
    }

    public function test_weak_password_is_rejected_on_step_one(): void
    {
        $response = $this->post('/register/step/1', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '9171234567',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_hub_can_be_rendered(): void
    {
        $response = $this->get('/join');

        $response->assertStatus(200);
        $response->assertSee('Join Talipapa');
        $response->assertSee('Store owner');
        $response->assertSee('Rider');
    }
}
