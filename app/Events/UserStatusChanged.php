<?php

namespace App\Events;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
        public UserStatus $previousStatus,
        public UserStatus $newStatus,
    ) {}
}
