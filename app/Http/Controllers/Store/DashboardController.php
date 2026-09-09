<?php

namespace App\Http\Controllers\Store;

use App\Enums\ApprovalStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $store = $request->user()->storeProfile;
        $storeId = $store?->id;

        $stats = [
            'pending_orders' => Order::query()
                ->where('store_profile_id', $storeId)
                ->where('status', OrderStatus::Pending)
                ->count(),
            'active_orders' => Order::query()
                ->where('store_profile_id', $storeId)
                ->whereIn('status', [
                    OrderStatus::Accepted,
                    OrderStatus::Preparing,
                    OrderStatus::ReadyForPickup,
                    OrderStatus::RiderAssigned,
                    OrderStatus::PickedUp,
                ])
                ->count(),
            'total_products' => $store?->products()->count() ?? 0,
            'today_sales' => Order::query()
                ->where('store_profile_id', $storeId)
                ->where('status', OrderStatus::Delivered)
                ->whereDate('updated_at', today())
                ->sum('total'),
        ];

        $recentOrders = Order::query()
            ->where('store_profile_id', $storeId)
            ->with(['customer', 'items'])
            ->latest()
            ->limit(5)
            ->get();

        return view('store.dashboard', compact('store', 'stats', 'recentOrders'));
    }
}
