<?php

namespace App\Http\Controllers;

use App\Http\Requests\Push\StorePushSubscriptionRequest;
use Illuminate\Http\JsonResponse;

class PushSubscriptionController extends Controller
{
    public function store(StorePushSubscriptionRequest $request): JsonResponse
    {
        $request->user()->updatePushSubscription(
            $request->input('endpoint'),
            $request->input('keys.p256dh'),
            $request->input('keys.auth'),
            'aesgcm',
        );

        return response()->json(['success' => true]);
    }
}
