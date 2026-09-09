<?php

namespace App\Http\Controllers;

use App\Services\PsgcService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PsgcController extends Controller
{
    public function __construct(
        private readonly PsgcService $psgcService,
    ) {}

    public function reverseGeocode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        return response()->json(
            $this->psgcService->reverseGeocode(
                (float) $validated['lat'],
                (float) $validated['lng'],
            )
        );
    }

    public function provinces(string $regionCode): JsonResponse
    {
        return response()->json(
            $this->psgcService->getProvinces($regionCode)->map(fn ($item) => [
                'code' => $item->code,
                'name' => $item->name,
            ]),
        );
    }

    public function cities(string $provinceCode): JsonResponse
    {
        return response()->json(
            $this->psgcService->getCities($provinceCode)->map(fn ($item) => [
                'code' => $item->code,
                'name' => $item->name,
            ]),
        );
    }

    public function barangays(string $cityCode): JsonResponse
    {
        return response()->json(
            $this->psgcService->getBarangays($cityCode)->map(fn ($item) => [
                'code' => $item->code,
                'name' => $item->name,
            ]),
        );
    }
}
