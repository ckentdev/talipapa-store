<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiderController extends Controller
{
    public function index(Request $request): View
    {
        $riders = User::query()
            ->where('role', 'rider')
            ->whereHas('riderProfile', fn ($q) => $q->approved()->available())
            ->with(['riderProfile', 'riderProfile.addresses'])
            ->orderBy('name')
            ->get();

        $riderStats = [
            'total' => $riders->count(),
            'with_gps' => $riders->filter(
                fn (User $rider) => $rider->riderProfile?->latitude && $rider->riderProfile?->longitude
            )->count(),
            'motorcycle' => $riders->filter(
                fn (User $rider) => strcasecmp($rider->riderProfile?->vehicle_type ?? '', 'Motorcycle') === 0
            )->count(),
            'other_vehicles' => $riders->reject(
                fn (User $rider) => strcasecmp($rider->riderProfile?->vehicle_type ?? '', 'Motorcycle') === 0
            )->count(),
        ];

        return view('store.riders.index', compact('riders', 'riderStats'));
    }
}
