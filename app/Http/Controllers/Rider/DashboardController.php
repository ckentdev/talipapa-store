<?php

namespace App\Http\Controllers\Rider;

use App\Enums\OrderStatus;
use App\Enums\RiderAvailability;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $profile = $user->riderProfile;

        $activeDelivery = Order::query()
            ->where('rider_id', $user->id)
            ->whereIn('status', [OrderStatus::RiderAssigned, OrderStatus::PickedUp])
            ->with(['store', 'address', 'customer', 'items'])
            ->first();

        $stats = [
            'today_deliveries' => Order::query()
                ->where('rider_id', $user->id)
                ->where('status', OrderStatus::Delivered)
                ->whereDate('updated_at', today())
                ->count(),
            'pending_offers' => Order::query()
                ->where('status', OrderStatus::ReadyForPickup)
                ->whereNull('rider_id')
                ->count(),
        ];

        return view('rider.dashboard', compact('profile', 'activeDelivery', 'stats'));
    }

    public function toggleAvailability(Request $request): RedirectResponse
    {
        $profile = $request->user()->riderProfile;

        if ($profile->status !== \App\Enums\ApprovalStatus::Approved) {
            return back()->with('warning', 'Your account must be approved before going online.');
        }

        $newAvailability = $profile->availability === RiderAvailability::Available
            ? RiderAvailability::Unavailable
            : RiderAvailability::Available;

        $profile->update(['availability' => $newAvailability]);

        $message = $newAvailability === RiderAvailability::Available
            ? 'You are now available for deliveries.'
            : 'You are now offline.';

        return back()->with('success', $message);
    }
}
