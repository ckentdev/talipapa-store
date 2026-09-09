<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $profile = $user->riderProfile?->load(['addresses', 'requirements']);

        return view('rider.account', compact('user', 'profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->riderProfile;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'vehicle_type' => 'required|string|max:50',
            'plate_number' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        $profile?->update([
            'vehicle_type' => $validated['vehicle_type'],
            'plate_number' => $validated['plate_number'] ?? null,
        ]);

        return back()->with('success', 'Account updated successfully.');
    }
}
