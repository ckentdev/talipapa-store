<?php

namespace App\Http\Controllers\Store;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $store = $request->user()->storeProfile;
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $orders = Order::query()
            ->where('store_profile_id', $store->id)
            ->where('status', OrderStatus::Delivered)
            ->whereDate('updated_at', '>=', $from)
            ->whereDate('updated_at', '<=', $to)
            ->with('items')
            ->latest()
            ->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_sales' => $orders->sum('total'),
            'total_subtotal' => $orders->sum('subtotal'),
            'total_delivery_fees' => $orders->sum('delivery_fee'),
        ];

        return view('store.reports.index', compact('orders', 'summary', 'from', 'to', 'store'));
    }
}
