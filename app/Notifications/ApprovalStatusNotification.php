<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ApprovalStatusNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public ?string $actionUrl = null,
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
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'message' => $this->message,
        ]);
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $message = (new WebPushMessage)
            ->title($this->title)
            ->body($this->message);

        if ($this->actionUrl) {
            $message->action('View', $this->actionUrl);
        }

        return $message;
    }
}
