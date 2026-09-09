<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Product $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::StoreOwner || $user->role === UserRole::Admin;
    }

    public function update(User $user, Product $product): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->role === UserRole::StoreOwner
            && $product->store_profile_id === $user->storeProfile?->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->update($user, $product);
    }
}
