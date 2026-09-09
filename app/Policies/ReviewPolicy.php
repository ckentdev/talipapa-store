<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Review;
use App\Models\User;
use App\Enums\OrderStatus;

class ReviewPolicy
{
    public function create(User $user, $order): bool
    {
        return $user->role === UserRole::Customer
            && $order->customer_id === $user->id
            && $order->status === OrderStatus::Delivered;
    }

    public function view(User $user, Review $review): bool
    {
        return true;
    }
}
