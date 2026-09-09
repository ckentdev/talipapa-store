<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ApprovalStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Concerns\HandlesDocumentUpload;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\StoreProfile;
use App\Models\StoreRequirement;
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

class StoreRegistrationController extends Controller
{
    use HandlesDocumentUpload;

    private const SESSION_KEY = 'store_registration';

    private const STEPS = [
        1 => 'Account',
        2 => 'Store Details',
        3 => 'Address',
        4 => 'Documents',
        5 => 'Review',
    ];

    public function __construct(
        private readonly PsgcService $psgcService,
    ) {}

    public function show(int $step = 1): View|RedirectResponse
    {
        if ($step < 1 || $step > 5) {
            return redirect()->route('register.store', ['step' => 1]);
        }

        if ($step > 1 && empty(session(self::SESSION_KEY))) {
            return redirect()->route('register.store', ['step' => 1]);
        }

        return view('auth.registration.store.register', [
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
            2 => $request->validate([
                'store_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:2000',
                'logo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            ]),
            3 => $request->validate(PsgcAddressRules::validation()),
            4 => $request->validate([
                'business_permit' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'valid_id' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'notes' => 'nullable|string|max:1000',
            ]),
            default => abort(404),
        };

        if ($step === 2 && $request->hasFile('logo')) {
            $validated['logo_path'] = $this->uploadDocument($request->file('logo'), 'store-logos');
        }

        if ($step === 3) {
            $validated = array_merge($validated, $this->psgcService->resolveAddressLabels($validated));
        }

        if ($step === 4) {
            $validated['business_permit_path'] = $this->uploadDocument($request->file('business_permit'), 'store-requirements');
            $validated['valid_id_path'] = $this->uploadDocument($request->file('valid_id'), 'store-requirements');
            unset($validated['business_permit'], $validated['valid_id']);
        }

        if ($step === 1 && isset($validated['phone'])) {
            $validated['phone'] = PhilippinePhone::normalize($validated['phone']);
        }

        session([self::SESSION_KEY => array_merge($data, $validated)]);

        if ($step === 4) {
            return redirect()->route('register.store', ['step' => 5]);
        }

        return redirect()->route('register.store', ['step' => $step + 1]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $data = session(self::SESSION_KEY, []);

        if (empty($data['email']) || empty($data['store_name']) || empty($data['business_permit_path'])) {
            return redirect()->route('register.store', ['step' => 1])
                ->with('error', 'Please complete all registration steps.');
        }

        $user = DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'role' => UserRole::StoreOwner,
                'status' => UserStatus::Active,
            ]);

            $store = StoreProfile::query()->create([
                'user_id' => $user->id,
                'store_name' => $data['store_name'],
                'description' => $data['description'] ?? null,
                'logo_path' => $data['logo_path'] ?? null,
                'status' => ApprovalStatus::Pending,
            ]);

            Address::query()->create([
                'user_id' => $user->id,
                'addressable_type' => StoreProfile::class,
                'addressable_id' => $store->id,
                'label' => $data['label'] ?? 'Store Location',
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

            StoreRequirement::query()->create([
                'store_profile_id' => $store->id,
                'business_permit_path' => $data['business_permit_path'],
                'valid_id_path' => $data['valid_id_path'],
                'notes' => $data['notes'] ?? null,
            ]);

            return $user;
        });

        session()->forget(self::SESSION_KEY);
        Auth::login($user);

        return redirect()->route('store.dashboard')
            ->with('success', 'Registration submitted! Your store is pending admin approval.');
    }
}
