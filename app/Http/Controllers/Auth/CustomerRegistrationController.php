<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CustomerProfile;
use App\Models\User;
use App\Services\PsgcService;
use App\Rules\PhilippinePhoneNumber;
use App\Rules\StrongPassword;
use App\Support\PhilippinePhone;
use App\Support\PsgcAddressRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Laravel\Jetstream\Jetstream;

class CustomerRegistrationController extends Controller
{
    private const SESSION_KEY = 'customer_registration';

    private const STEPS = [
        1 => 'Account',
        2 => 'Address',
        3 => 'Preferences',
        4 => 'Review',
    ];

    public function __construct(
        private readonly PsgcService $psgcService,
    ) {}

    public function show(int $step = 1): View|RedirectResponse
    {
        if ($step < 1 || $step > 4) {
            return redirect()->route('register', ['step' => 1]);
        }

        if ($step > 1 && empty(session(self::SESSION_KEY))) {
            return redirect()->route('register', ['step' => 1]);
        }

        return view('auth.registration.customer.register', [
            'step' => $step,
            'steps' => self::STEPS,
            'data' => session(self::SESSION_KEY, []),
            'regions' => $this->psgcService->getRegions(),
        ]);
    }

    public function storeStep(Request $request, int $step): RedirectResponse
    {
        $data = session(self::SESSION_KEY, []);

        $validated = match ($step) {
            1 => $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'phone' => ['required', 'string', 'max:20', new PhilippinePhoneNumber],
                'password' => ['required', 'string', 'confirmed', new StrongPassword],
            ]),
            2 => $request->validate(PsgcAddressRules::validation()),
            3 => $request->validate([
                'delivery_instructions' => 'nullable|string|max:500',
                'contact_preferences' => 'required|array|min:1',
                'contact_preferences.*' => 'in:phone,sms,email',
                'marketing_opt_in' => 'sometimes|boolean',
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            ]),
            default => abort(404),
        };

        if ($step === 2) {
            $validated = array_merge($validated, $this->psgcService->resolveAddressLabels($validated));
        }

        if ($step === 3) {
            $validated['marketing_opt_in'] = $request->boolean('marketing_opt_in');
            $validated['contact_preferences'] = array_values(array_unique($validated['contact_preferences']));
        }

        if ($step === 1 && isset($validated['phone'])) {
            $validated['phone'] = PhilippinePhone::normalize($validated['phone']);
        }

        session([self::SESSION_KEY => array_merge($data, $validated)]);

        return redirect()->route('register', ['step' => $step + 1]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $data = session(self::SESSION_KEY, []);

        if (
            empty($data['email'])
            || empty($data['street_address'])
            || empty($data['contact_preferences'])
        ) {
            return redirect()->route('register', ['step' => 1])
                ->with('error', 'Please complete all registration steps.');
        }

        $user = DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'role' => UserRole::Customer,
                'status' => UserStatus::Active,
            ]);

            $profile = CustomerProfile::query()->create([
                'user_id' => $user->id,
                'preferences' => [
                    'delivery_instructions' => $data['delivery_instructions'] ?? null,
                    'contact_preferences' => array_values($data['contact_preferences'] ?? []),
                    'marketing_opt_in' => (bool) ($data['marketing_opt_in'] ?? false),
                ],
            ]);

            Address::query()->create([
                'user_id' => $user->id,
                'addressable_type' => CustomerProfile::class,
                'addressable_id' => $profile->id,
                'label' => $data['label'] ?? 'Home',
                'region_code' => $data['region_code'],
                'province_code' => $data['province_code'],
                'city_code' => $data['city_code'],
                'barangay_code' => $data['barangay_code'],
                'street_address' => $data['street_address'],
                'postal_code' => $data['postal_code'] ?? null,
                'landmark' => $data['landmark'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'is_default' => true,
            ]);

            return $user;
        });

        session()->forget(self::SESSION_KEY);
        Auth::login($user);

        return redirect()->route('landing')
            ->with('success', 'Welcome to Talipapa! Your account is ready.');
    }
}
