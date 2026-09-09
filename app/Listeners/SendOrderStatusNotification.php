<?php

namespace App\Listeners;

use App\Enums\OrderStatus;
use App\Events\OrderStatusChanged;
use App\Notifications\OrderStatusNotification;

class SendOrderStatusNotification
{
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order->load(['customer', 'store.user', 'rider']);
        $status = $event->newStatus;

        $messages = match ($status) {
            OrderStatus::Pending => [
                'store' => ['New Order', "New order {$order->order_number} received."],
            ],
            OrderStatus::Accepted => [
                'customer' => ['Order Accepted', "Your order {$order->order_number} was accepted."],
            ],
            OrderStatus::Rejected => [
                'customer' => ['Order Rejected', "Your order {$order->order_number} was rejected."],
            ],
            OrderStatus::ReadyForPickup => [
                'customer' => ['Ready for Pickup', "Order {$order->order_number} is ready for pickup."],
                'rider' => ['Delivery Available', "Order {$order->order_number} is ready for pickup."],
            ],
            OrderStatus::RiderAssigned => [
                'rider' => ['New Assignment', "You were assigned to order {$order->order_number}."],
                'customer' => ['Rider Assigned', "A rider was assigned to order {$order->order_number}."],
            ],
            OrderStatus::PickedUp => [
                'customer' => ['Order Picked Up', "Your order {$order->order_number} is on the way."],
                'store' => ['Order Picked Up', "Order {$order->order_number} was picked up."],
            ],
            OrderStatus::Delivered => [
                'customer' => ['Order Delivered', "Order {$order->order_number} has been delivered."],
                'store' => ['Order Delivered', "Order {$order->order_number} was delivered."],
            ],
            default => [],
        };

        if (isset($messages['customer'])) {
            $order->customer->notify(new OrderStatusNotification($order, ...$messages['customer']));
        }

        if (isset($messages['store']) && $order->store?->user) {
            $order->store->user->notify(new OrderStatusNotification($order, ...$messages['store']));
        }

        if (isset($messages['rider']) && $order->rider) {
            $order->rider->notify(new OrderStatusNotification($order, ...$messages['rider']));
        }
    }
}
