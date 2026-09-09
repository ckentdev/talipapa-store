<?php

namespace App\Events;

use App\Enums\ApprovalStatus;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class ApprovalStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Model $profile,
        public ApprovalStatus $previousStatus,
        public ApprovalStatus $newStatus,
    ) {}
}
