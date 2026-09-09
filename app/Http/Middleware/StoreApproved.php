<?php

namespace App\Http\Middleware;

use App\Enums\ApprovalStatus;
use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== UserRole::StoreOwner) {
            abort(403);
        }

        $store = $user->storeProfile;

        if (! $store || $store->status !== ApprovalStatus::Approved) {
            return redirect()
                ->route('store.dashboard')
                ->with('warning', 'Your store must be approved before accessing this feature.');
        }

        return $next($request);
    }
}
