<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PsgcService
{
    /**
     * @return Collection<int, object{code: string, name: string}>
     */
    public function getRegions(): Collection
    {
        return $this->asOptions($this->fetch('/regions.json'));
    }

    /**
     * Provinces for a region; NCR and similar areas use legislative districts instead.
     *
     * @return Collection<int, object{code: string, name: string}>
     */
    public function getProvinces(string $regionCode): Collection
    {
        $items = $this->fetch("/regions/{$regionCode}/provinces.json");

        if ($items === []) {
            $items = $this->fetch("/regions/{$regionCode}/districts.json");
        }

        return $this->asOptions($items);
    }

    /**
     * Cities / municipalities under a province or NCR district.
     *
     * @return Collection<int, object{code: string, name: string}>
     */
    public function getCities(string $provinceOrDistrictCode): Collection
    {
        $items = $this->fetch("/provinces/{$provinceOrDistrictCode}/cities-municipalities.json");

        if ($items === []) {
            $items = $this->fetch("/districts/{$provinceOrDistrictCode}/cities-municipalities.json");
        }

        return $this->asOptions($items);
    }

    /**
     * @return Collection<int, object{code: string, name: string}>
     */
    public function getBarangays(string $cityCode): Collection
    {
        return $this->asOptions($this->fetch("/cities-municipalities/{$cityCode}/barangays.json"));
    }

    public function nameForCode(string $code, ?string $parentCode = null): ?string
    {
        $code = trim($code);
        if ($code === '') {
            return null;
        }

        foreach ($this->getRegions() as $region) {
            if ($region->code === $code) {
                return $region->name;
            }
        }

        if ($parentCode) {
            return $this->findNameIn($this->getProvinces($parentCode), $code)
                ?? $this->findNameIn($this->getCities($parentCode), $code);
        }

        return null;
    }

    public function resolveAddressLabels(array $address): array
    {
        $regionCode = $address['region_code'] ?? '';
        $provinceCode = $address['province_code'] ?? '';
        $cityCode = $address['city_code'] ?? '';
        $barangayCode = $address['barangay_code'] ?? '';

        return [
            'region_name' => $this->findNameIn($this->getRegions(), $regionCode),
            'province_name' => $this->findNameIn($this->getProvinces($regionCode), $provinceCode),
            'city_name' => $this->findNameIn($this->getCities($provinceCode), $cityCode),
            'barangay_name' => $this->findNameIn($this->getBarangays($cityCode), $barangayCode),
        ];
    }

    /**
     * Reverse geocode coordinates and match to PSGC codes.
     *
     * @return array<string, mixed>
     */
    public function reverseGeocode(float $latitude, float $longitude): array
    {
        $cacheKey = 'psgc:reverse:'.round($latitude, 5).':'.round($longitude, 5);

        return Cache::remember($cacheKey, config('psgc.cache_ttl'), function () use ($latitude, $longitude) {
            $response = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => config('app.name', 'Talipapa').'/1.0 ('.config('app.url').')',
                ])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'format' => 'json',
                    'addressdetails' => 1,
                ]);

            if (! $response->successful()) {
                return [
                    'street_address' => null,
                    'postal_code' => null,
                    'display_name' => null,
                ];
            }

            $payload = $response->json();
            $parts = is_array($payload['address'] ?? null) ? $payload['address'] : [];

            $region = $this->matchRegion($parts);
            $province = $region ? $this->matchProvince($region->code, $parts) : null;
            $city = $this->matchCity($region?->code, $province?->code, $parts);
            $province = $province ?? ($city['province'] ?? null);
            $barangay = $city['item'] ? $this->matchBarangay($city['item']->code, $parts) : null;

            return [
                'region_code' => $region?->code,
                'province_code' => $province?->code,
                'city_code' => $city['item']?->code,
                'barangay_code' => $barangay?->code,
                'region_name' => $region?->name,
                'province_name' => $province?->name,
                'city_name' => $city['item']?->name,
                'barangay_name' => $barangay?->name,
                'street_address' => $this->buildStreetLine($parts),
                'postal_code' => $parts['postcode'] ?? null,
                'display_name' => $payload['display_name'] ?? null,
            ];
        });
    }

    /**
     * @param  array<string, string>  $parts
     */
    private function matchRegion(array $parts): ?object
    {
        $candidates = array_filter([
            $parts['region'] ?? null,
            $parts['state'] ?? null,
            $parts['state_district'] ?? null,
        ]);

        foreach ($this->getRegions() as $region) {
            foreach ($candidates as $candidate) {
                if ($this->namesMatch($candidate, $region->name)) {
                    return $region;
                }
            }
        }

        if ($this->isNcrCandidate($candidates)) {
            return $this->getRegions()->firstWhere('code', '130000000');
        }

        return null;
    }

    /**
     * @param  array<string, string>  $parts
     */
    private function matchProvince(string $regionCode, array $parts): ?object
    {
        $candidates = array_filter([
            $parts['province'] ?? null,
            $parts['county'] ?? null,
            $parts['city_district'] ?? null,
            $parts['state_district'] ?? null,
        ]);

        return $this->matchFromCollection($this->getProvinces($regionCode), $candidates);
    }

    /**
     * @param  array<string, string>  $parts
     * @return array{item: ?object, province: ?object}
     */
    private function matchCity(?string $regionCode, ?string $provinceCode, array $parts): array
    {
        $candidates = array_filter([
            $parts['city'] ?? null,
            $parts['town'] ?? null,
            $parts['municipality'] ?? null,
        ]);

        if ($provinceCode) {
            $match = $this->matchFromCollection($this->getCities($provinceCode), $candidates);
            if ($match) {
                return ['item' => $match, 'province' => null];
            }
        }

        if (! $regionCode) {
            return ['item' => null, 'province' => null];
        }

        foreach ($this->getProvinces($regionCode) as $province) {
            $match = $this->matchFromCollection($this->getCities($province->code), $candidates);
            if ($match) {
                return ['item' => $match, 'province' => $province];
            }
        }

        return ['item' => null, 'province' => null];
    }

    /**
     * @param  array<string, string>  $parts
     */
    private function matchBarangay(string $cityCode, array $parts): ?object
    {
        $candidates = array_filter([
            $parts['suburb'] ?? null,
            $parts['quarter'] ?? null,
            $parts['neighbourhood'] ?? null,
            $parts['village'] ?? null,
            $parts['hamlet'] ?? null,
        ]);

        return $this->matchFromCollection($this->getBarangays($cityCode), $candidates);
    }

    /**
     * @param  array<string, string>  $parts
     */
    private function buildStreetLine(array $parts): ?string
    {
        $segments = array_filter([
            $parts['house_number'] ?? null,
            $parts['road'] ?? null,
            $parts['residential'] ?? null,
            $parts['commercial'] ?? null,
        ]);

        return $segments !== [] ? implode(', ', $segments) : null;
    }

    /**
     * @param  Collection<int, object{code: string, name: string}>  $items
     * @param  array<int, string>  $candidates
     */
    private function matchFromCollection(Collection $items, array $candidates): ?object
    {
        foreach ($candidates as $candidate) {
            foreach ($items as $item) {
                if ($this->namesMatch($candidate, $item->name)) {
                    return $item;
                }
            }
        }

        foreach ($candidates as $candidate) {
            $normalizedCandidate = $this->normalizeName($candidate);
            foreach ($items as $item) {
                $normalizedItem = $this->normalizeName($item->name);
                if (Str::contains($normalizedItem, $normalizedCandidate) || Str::contains($normalizedCandidate, $normalizedItem)) {
                    return $item;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<int, string>  $candidates
     */
    private function isNcrCandidate(array $candidates): bool
    {
        foreach ($candidates as $candidate) {
            $normalized = $this->normalizeName($candidate);
            if (Str::contains($normalized, 'manila') || Str::contains($normalized, 'ncr') || Str::contains($normalized, 'metro')) {
                return true;
            }
        }

        return false;
    }

    private function namesMatch(?string $left, ?string $right): bool
    {
        if (! filled($left) || ! filled($right)) {
            return false;
        }

        return $this->normalizeName($left) === $this->normalizeName($right);
    }

    private function normalizeName(string $name): string
    {
        $name = Str::lower(trim($name));
        $name = str_replace(['.', '-', '_'], '', $name);
        $name = preg_replace('/\s+/', ' ', $name) ?? $name;

        return trim($name);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetch(string $path): array
    {
        $path = Str::start($path, '/');
        $cacheKey = 'psgc:'.md5($path);

        return Cache::remember($cacheKey, config('psgc.cache_ttl'), function () use ($path) {
            $url = rtrim(config('psgc.base_url'), '/').$path;

            $response = Http::timeout(20)
                ->acceptJson()
                ->get($url);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();

            return is_array($data) ? $data : [];
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return Collection<int, object{code: string, name: string}>
     */
    private function asOptions(array $items): Collection
    {
        return collect($items)
            ->filter(fn (array $item) => filled($item['code'] ?? null) && filled($item['name'] ?? null))
            ->map(fn (array $item) => (object) [
                'code' => (string) $item['code'],
                'name' => (string) $item['name'],
            ])
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /**
     * @param  Collection<int, object{code: string, name: string}>  $items
     */
    private function findNameIn(Collection $items, string $code): ?string
    {
        if ($code === '') {
            return null;
        }

        $match = $items->firstWhere('code', $code);

        return $match?->name;
    }
}
