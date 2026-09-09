<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdatePermissionRequest;
use App\Http\Requests\Account\UpdateSoundAlertsRequest;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function update(UpdatePermissionRequest $request): JsonResponse
    {
        $field = match ($request->input('permission')) {
            'location' => 'location_permission',
            'microphone' => 'microphone_permission',
            'push' => 'push_permission',
        };

        $request->user()->update([$field => $request->boolean('granted')]);

        return response()->json(['success' => true]);
    }

    public function updateSoundAlerts(UpdateSoundAlertsRequest $request): JsonResponse
    {
        $request->user()->update([
            'sound_alerts_enabled' => $request->boolean('enabled'),
        ]);

        return response()->json(['success' => true]);
    }
}
