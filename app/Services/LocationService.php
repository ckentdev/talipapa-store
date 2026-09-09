<?php

namespace App\Services;

use App\Models\StoreProfile;
use Illuminate\Database\Eloquent\Collection;

class LocationService
{
    private const EARTH_RADIUS_KM = 6371;

    public function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_KM * $c;
    }

    /**
     * @return Collection<int, StoreProfile>
     */
    public function findNearbyStores(float $lat, float $lng, float $radiusKm): Collection
    {
        $stores = StoreProfile::query()
            ->approved()
            ->with(['addresses' => fn ($query) => $query
                ->whereNotNull('latitude')
                ->whereNotNull('longitude'),
            ])
            ->get();

        return $stores
            ->map(function (StoreProfile $store) use ($lat, $lng, $radiusKm): ?StoreProfile {
                $nearestDistance = null;

                foreach ($store->addresses as $address) {
                    $distance = $this->haversineDistance(
                        $lat,
                        $lng,
                        (float) $address->latitude,
                        (float) $address->longitude,
                    );

                    if ($distance <= $radiusKm && ($nearestDistance === null || $distance < $nearestDistance)) {
                        $nearestDistance = $distance;
                    }
                }

                if ($nearestDistance === null) {
                    return null;
                }

                $store->setAttribute('distance_km', round($nearestDistance, 2));

                return $store;
            })
            ->filter()
            ->sortBy('distance_km')
            ->values();
    }
}
