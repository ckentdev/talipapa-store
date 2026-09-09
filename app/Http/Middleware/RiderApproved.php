<?php

namespace App\Http\Middleware;

use App\Enums\ApprovalStatus;
use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RiderApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== UserRole::Rider) {
            abort(403);
        }

        $rider = $user->riderProfile;

        if (! $rider || $rider->status !== ApprovalStatus::Approved) {
            return redirect()
                ->route('rider.dashboard')
                ->with('warning', 'Your rider account must be approved before accessing this feature.');
        }

        return $next($request);
    }
}
