<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        if ($user->role === UserRole::Customer) {
            return $order->customer_id === $user->id;
        }

        if ($user->role === UserRole::StoreOwner) {
            return $order->store_profile_id === $user->storeProfile?->id;
        }

        if ($user->role === UserRole::Rider) {
            return $order->rider_id === $user->id;
        }

        return false;
    }

    public function update(User $user, Order $order): bool
    {
        return $this->view($user, $order);
    }
}
