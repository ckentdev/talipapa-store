<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function show(Request $request, string $id): RedirectResponse
    {
        /** @var DatabaseNotification $notification */
        $notification = $request->user()->notifications()->whereKey($id)->firstOrFail();

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        $orderId = data_get($notification->data, 'order_id');
        $user = $request->user();

        if ($orderId) {
            if ($user->isCustomer()) {
                return redirect()->route('customer.orders.show', $orderId);
            }

            if ($user->isStoreOwner()) {
                return redirect()->route('store.orders.show', $orderId);
            }

            if ($user->isRider()) {
                return redirect()->route('rider.deliveries');
            }

            if ($user->isAdmin()) {
                return redirect()->route('admin.orders.show', $orderId);
            }
        }

        return redirect()->route('notifications.index');
    }
}
