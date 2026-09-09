<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Events\OrderStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    public function index(Request $request): View
    {
        $query = Order::query()->with(['customer', 'store', 'rider', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'store', 'rider', 'address', 'items.product']);

        $timeline = collect([
            OrderStatus::Pending,
            OrderStatus::Accepted,
            OrderStatus::Preparing,
            OrderStatus::ReadyForPickup,
            OrderStatus::RiderAssigned,
            OrderStatus::PickedUp,
            OrderStatus::Delivered,
        ]);

        $availableRiders = User::query()
            ->where('role', 'rider')
            ->whereHas('riderProfile', fn ($q) => $q->approved())
            ->with('riderProfile')
            ->get();

        return view('admin.orders.show', compact('order', 'availableRiders', 'timeline'));
    }

    public function assignRider(Request $request, Order $order): RedirectResponse
    {
        $request->validate(['rider_id' => 'required|exists:users,id']);

        $rider = User::query()->findOrFail($request->input('rider_id'));
        $previous = $order->status;

        $order = $this->orderService->assignRider($order, $rider);

        OrderStatusChanged::dispatch($order, $previous, $order->status);

        return back()->with('success', 'Rider assigned.');
    }
}
