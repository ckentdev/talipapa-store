<?php

namespace App\Http\Controllers\Rider;

use App\Enums\OrderStatus;
use App\Enums\RiderAvailability;
use App\Events\OrderStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $assigned = Order::query()
            ->where('rider_id', $user->id)
            ->whereIn('status', [OrderStatus::RiderAssigned, OrderStatus::PickedUp])
            ->with(['store', 'address', 'customer', 'items'])
            ->latest()
            ->get();

        $available = Order::query()
            ->where('status', OrderStatus::ReadyForPickup)
            ->whereNull('rider_id')
            ->with(['store', 'address'])
            ->latest()
            ->get();

        $completed = Order::query()
            ->where('rider_id', $user->id)
            ->where('status', OrderStatus::Delivered)
            ->with(['store'])
            ->latest()
            ->limit(10)
            ->get();

        return view('rider.deliveries.index', compact('assigned', 'available', 'completed'));
    }

    public function accept(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->status === OrderStatus::ReadyForPickup && $order->rider_id === null, 403);

        $previous = $order->status;
        $order = $this->orderService->assignRider($order, $request->user());

        $request->user()->riderProfile?->update(['availability' => RiderAvailability::OnDelivery]);

        OrderStatusChanged::dispatch($order, $previous, $order->status);

        return back()->with('success', 'Delivery accepted.');
    }

    public function decline(Request $request, Order $order): RedirectResponse
    {
        return back()->with('success', 'Delivery declined.');
    }

    public function pickup(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->rider_id === $request->user()->id, 403);

        $previous = $order->status;
        $order = $this->orderService->updateStatus($order, OrderStatus::PickedUp);

        OrderStatusChanged::dispatch($order, $previous, OrderStatus::PickedUp);

        return back()->with('success', 'Order picked up.');
    }

    public function deliver(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->rider_id === $request->user()->id, 403);

        $previous = $order->status;
        $order = $this->orderService->updateStatus($order, OrderStatus::Delivered);

        $request->user()->riderProfile?->update(['availability' => RiderAvailability::Available]);

        OrderStatusChanged::dispatch($order, $previous, OrderStatus::Delivered);

        return back()->with('success', 'Order delivered successfully.');
    }
}
