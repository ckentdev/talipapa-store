<?php

namespace App\Http\Controllers\Store;

use App\Enums\ApprovalStatus;
use App\Http\Controllers\Concerns\HandlesDocumentUpload;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\StoreProfile;
use App\Services\PsgcService;
use App\Support\PsgcAddressRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreInformationController extends Controller
{
    use HandlesDocumentUpload;

    public function __construct(
        private readonly PsgcService $psgcService,
    ) {}

    public function show(Request $request): View
    {
        $user = $request->user();
        $store = $user->storeProfile?->load([
            'addresses.region',
            'addresses.province',
            'addresses.city',
            'addresses.barangay',
            'requirements',
        ]);

        $isApproved = $store && $store->status === ApprovalStatus::Approved;
        $regions = $this->psgcService->getRegions();

        return view('store.information', compact('user', 'store', 'regions', 'isApproved'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $store = $user->storeProfile;

        abort_unless($store, 404);

        $isApproved = $store->status === ApprovalStatus::Approved;

        $rules = [
            'store_name' => 'required|string|max:255',
        ];

        if ($isApproved) {
            $rules = array_merge($rules, [
                'description' => 'nullable|string|max:2000',
                'logo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
                'cover' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            ], PsgcAddressRules::validation());
        }

        $validated = $request->validate($rules);

        $storeUpdates = [
            'store_name' => $validated['store_name'],
        ];

        if ($isApproved) {
            $storeUpdates['description'] = $validated['description'] ?? null;

            if ($request->hasFile('logo')) {
                $storeUpdates['logo_path'] = $this->uploadDocument($request->file('logo'), 'store-logos');
            }

            if ($request->hasFile('cover')) {
                $storeUpdates['cover_path'] = $this->uploadDocument($request->file('cover'), 'store-covers');
            }

            $store->update($storeUpdates);

            $addressData = [
                'region_code' => $validated['region_code'],
                'province_code' => $validated['province_code'],
                'city_code' => $validated['city_code'],
                'barangay_code' => $validated['barangay_code'],
                'street_address' => $validated['street_address'],
                'postal_code' => $validated['postal_code'] ?? null,
                'landmark' => $validated['landmark'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
            ];

            if (isset($validated['label'])) {
                $addressData['label'] = $validated['label'];
            }

            $address = $store->addresses()->first();

            if ($address) {
                $address->update($addressData);
            } else {
                Address::query()->create([
                    ...$addressData,
                    'user_id' => $user->id,
                    'addressable_type' => StoreProfile::class,
                    'addressable_id' => $store->id,
                    'label' => $addressData['label'] ?? 'Store Location',
                    'is_default' => true,
                ]);
            }
        } else {
            $store->update($storeUpdates);
        }

        return back()->with('success', 'Store information updated successfully.');
    }
}
