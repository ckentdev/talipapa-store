<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with(['store', 'items.product'])
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->customer_id === $request->user()->id, 403);

        $order->load(['store', 'rider', 'address', 'items.product', 'reviews']);

        $timeline = collect([
            OrderStatus::Pending,
            OrderStatus::Accepted,
            OrderStatus::Preparing,
            OrderStatus::ReadyForPickup,
            OrderStatus::RiderAssigned,
            OrderStatus::PickedUp,
            OrderStatus::Delivered,
        ]);

        return view('customer.orders.show', compact('order', 'timeline'));
    }
}
