<?php

namespace App\Http\Controllers\Store;

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
        $store = $request->user()->storeProfile;

        $query = Order::query()
            ->where('store_profile_id', $store->id)
            ->with(['customer', 'rider', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest()->paginate(15);

        return view('store.orders.index', compact('orders', 'store'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->store_profile_id === $request->user()->storeProfile->id, 403);

        $order->load(['customer', 'rider', 'address', 'items.product']);

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
            ->whereHas('riderProfile', fn ($q) => $q->approved()->available())
            ->with('riderProfile')
            ->get();

        return view('store.orders.show', compact('order', 'availableRiders', 'timeline'));
    }

    public function accept(Request $request, Order $order): RedirectResponse
    {
        return $this->transition($request, $order, OrderStatus::Accepted);
    }

    public function reject(Request $request, Order $order): RedirectResponse
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        return $this->transition($request, $order, OrderStatus::Rejected, [
            'rejection_reason' => $request->input('rejection_reason'),
        ]);
    }

    public function preparing(Request $request, Order $order): RedirectResponse
    {
        return $this->transition($request, $order, OrderStatus::Preparing);
    }

    public function ready(Request $request, Order $order): RedirectResponse
    {
        return $this->transition($request, $order, OrderStatus::ReadyForPickup);
    }

    public function assignRider(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->store_profile_id === $request->user()->storeProfile->id, 403);

        $request->validate(['rider_id' => 'required|exists:users,id']);

        $rider = User::query()->findOrFail($request->input('rider_id'));
        $previous = $order->status;

        $order = $this->orderService->assignRider($order, $rider);

        OrderStatusChanged::dispatch($order, $previous, $order->status);

        return back()->with('success', 'Rider assigned successfully.');
    }

    private function transition(Request $request, Order $order, OrderStatus $status, array $extra = []): RedirectResponse
    {
        abort_unless($order->store_profile_id === $request->user()->storeProfile->id, 403);

        $previous = $order->status;
        $order = $this->orderService->updateStatus($order, $status, $extra);

        OrderStatusChanged::dispatch($order, $previous, $status);

        return back()->with('success', "Order marked as {$status->label()}.");
    }
}
