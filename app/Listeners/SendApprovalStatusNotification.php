<?php

namespace App\Listeners;

use App\Enums\ApprovalStatus;
use App\Events\ApprovalStatusChanged;
use App\Notifications\ApprovalStatusNotification;

class SendApprovalStatusNotification
{
    public function handle(ApprovalStatusChanged $event): void
    {
        $user = $event->profile->user;
        $type = $event->profile instanceof \App\Models\StoreProfile ? 'store' : 'rider';
        $status = $event->newStatus;

        [$title, $message, $url] = match ($status) {
            ApprovalStatus::Approved => [
                ucfirst($type).' Approved',
                "Your {$type} account has been approved.",
                $type === 'store' ? route('store.dashboard') : route('rider.dashboard'),
            ],
            ApprovalStatus::Rejected => [
                ucfirst($type).' Rejected',
                "Your {$type} application was rejected.",
                $type === 'store' ? route('store.information') : route('rider.account'),
            ],
            ApprovalStatus::Suspended => [
                ucfirst($type).' Suspended',
                "Your {$type} account has been suspended.",
                null,
            ],
            default => [null, null, null],
        };

        if ($title) {
            $user->notify(new ApprovalStatusNotification($title, $message, $url));
        }
    }
}
