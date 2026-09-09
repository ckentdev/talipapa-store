<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ApprovalStatus;
use App\Enums\RiderAvailability;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Concerns\HandlesDocumentUpload;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\RiderProfile;
use App\Models\RiderRequirement;
use App\Models\User;
use App\Rules\PhilippinePhoneNumber;
use App\Rules\StrongPassword;
use App\Services\PsgcService;
use App\Support\PhilippinePhone;
use App\Support\PsgcAddressRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RiderRegistrationController extends Controller
{
    use HandlesDocumentUpload;

    private const SESSION_KEY = 'rider_registration';

    private const STEPS = [
        1 => 'Account',
        2 => 'Vehicle',
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
            return redirect()->route('register.rider', ['step' => 1]);
        }

        if ($step > 1 && empty(session(self::SESSION_KEY))) {
            return redirect()->route('register.rider', ['step' => 1]);
        }

        return view('auth.registration.rider.register', [
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
                'vehicle_type' => 'required|string|max:50',
                'plate_number' => 'nullable|string|max:20',
            ]),
            3 => $request->validate(PsgcAddressRules::validation()),
            4 => $request->validate([
                'valid_id' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'driver_license' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'notes' => 'nullable|string|max:1000',
            ]),
            default => abort(404),
        };

        if ($step === 3) {
            $validated = array_merge($validated, $this->psgcService->resolveAddressLabels($validated));
        }

        if ($step === 4) {
            $validated['valid_id_path'] = $this->uploadDocument($request->file('valid_id'), 'rider-requirements');
            $validated['driver_license_path'] = $this->uploadDocument($request->file('driver_license'), 'rider-requirements');
            unset($validated['valid_id'], $validated['driver_license']);
        }

        if ($step === 1 && isset($validated['phone'])) {
            $validated['phone'] = PhilippinePhone::normalize($validated['phone']);
        }

        session([self::SESSION_KEY => array_merge($data, $validated)]);

        if ($step === 4) {
            return redirect()->route('register.rider', ['step' => 5]);
        }

        return redirect()->route('register.rider', ['step' => $step + 1]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $data = session(self::SESSION_KEY, []);

        if (empty($data['email']) || empty($data['vehicle_type']) || empty($data['valid_id_path'])) {
            return redirect()->route('register.rider', ['step' => 1])
                ->with('error', 'Please complete all registration steps.');
        }

        $user = DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'role' => UserRole::Rider,
                'status' => UserStatus::Active,
            ]);

            $rider = RiderProfile::query()->create([
                'user_id' => $user->id,
                'vehicle_type' => $data['vehicle_type'],
                'plate_number' => $data['plate_number'] ?? null,
                'status' => ApprovalStatus::Pending,
                'availability' => RiderAvailability::Unavailable,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
            ]);

            Address::query()->create([
                'user_id' => $user->id,
                'addressable_type' => RiderProfile::class,
                'addressable_id' => $rider->id,
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

            RiderRequirement::query()->create([
                'rider_profile_id' => $rider->id,
                'valid_id_path' => $data['valid_id_path'],
                'driver_license_path' => $data['driver_license_path'],
                'notes' => $data['notes'] ?? null,
            ]);

            return $user;
        });

        session()->forget(self::SESSION_KEY);
        Auth::login($user);

        return redirect()->route('rider.dashboard')
            ->with('success', 'Registration submitted! Your rider account is pending admin approval.');
    }
}
