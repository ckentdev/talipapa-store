<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function show(Request $request): View
    {
        $profile = $request->user()->riderProfile;

        return view('rider.location', compact('profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $request->user()->riderProfile?->update($validated);

        return back()->with('success', 'Location updated.');
    }
}
