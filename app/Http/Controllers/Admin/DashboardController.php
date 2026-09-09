<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApprovalStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RiderProfile;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $kpis = [
            'total_users' => User::query()->count(),
            'total_stores' => StoreProfile::query()->count(),
            'pending_stores' => StoreProfile::query()->where('status', ApprovalStatus::Pending)->count(),
            'pending_riders' => RiderProfile::query()->where('status', ApprovalStatus::Pending)->count(),
            'active_orders' => Order::query()
                ->whereNotIn('status', [OrderStatus::Delivered, OrderStatus::Rejected, OrderStatus::Cancelled])
                ->count(),
            'today_orders' => Order::query()->whereDate('created_at', today())->count(),
            'today_revenue' => Order::query()
                ->where('status', OrderStatus::Delivered)
                ->whereDate('updated_at', today())
                ->sum('total'),
        ];

        $recentOrders = Order::query()
            ->with(['customer', 'store'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('kpis', 'recentOrders'));
    }
}
