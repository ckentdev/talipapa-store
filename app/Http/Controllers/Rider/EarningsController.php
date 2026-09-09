<?php

namespace App\Http\Controllers\Rider;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EarningsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $orders = Order::query()
            ->where('rider_id', $user->id)
            ->where('status', OrderStatus::Delivered)
            ->with(['store'])
            ->latest()
            ->paginate(20);

        $summary = [
            'total_deliveries' => Order::query()
                ->where('rider_id', $user->id)
                ->where('status', OrderStatus::Delivered)
                ->count(),
            'today_deliveries' => Order::query()
                ->where('rider_id', $user->id)
                ->where('status', OrderStatus::Delivered)
                ->whereDate('updated_at', today())
                ->count(),
            'total_earnings' => Order::query()
                ->where('rider_id', $user->id)
                ->where('status', OrderStatus::Delivered)
                ->sum('delivery_fee'),
        ];

        return view('rider.earnings.index', compact('orders', 'summary'));
    }
}
