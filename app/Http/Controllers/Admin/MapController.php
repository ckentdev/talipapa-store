<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\RiderProfile;
use App\Models\StoreProfile;
use Illuminate\View\View;

class MapController extends Controller
{
    public function index(): View
    {
        $stores = StoreProfile::query()
            ->approved()
            ->with(['addresses'])
            ->get()
            ->flatMap(fn (StoreProfile $store) => $store->addresses
                ->filter(fn ($a) => $a->latitude && $a->longitude)
                ->map(fn ($a) => [
                    'type' => 'store',
                    'name' => $store->store_name,
                    'lat' => (float) $a->latitude,
                    'lng' => (float) $a->longitude,
                ]));

        $riders = RiderProfile::query()
            ->approved()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('user')
            ->get()
            ->map(fn (RiderProfile $rider) => [
                'type' => 'rider',
                'name' => $rider->user->name,
                'lat' => (float) $rider->latitude,
                'lng' => (float) $rider->longitude,
            ]);

        $customers = Address::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('user')
            ->limit(100)
            ->get()
            ->map(fn (Address $address) => [
                'type' => 'customer',
                'name' => $address->user?->name ?? 'Customer',
                'lat' => (float) $address->latitude,
                'lng' => (float) $address->longitude,
            ]);

        $locations = $stores->concat($riders)->concat($customers)->values();

        return view('admin.map.index', compact('locations'));
    }
}
