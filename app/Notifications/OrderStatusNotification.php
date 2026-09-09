<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class OrderStatusNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public string $title,
        public string $message,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database', 'broadcast'];

        if ($notifiable->push_permission && $notifiable->pushSubscriptions()->exists()) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'title' => $this->title,
            'message' => $this->message,
            'status' => $this->order->status->value,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'message' => $this->message,
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
        ]);
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->body($this->message)
            ->action('View', route('customer.orders.show', $this->order))
            ->data(['order_id' => $this->order->id]);
    }
}
