<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case StoreOwner = 'store_owner';
    case Rider = 'rider';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Customer',
            self::StoreOwner => 'Store Owner',
            self::Rider => 'Rider',
            self::Admin => 'Admin',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Customer => 'landing',
            self::StoreOwner => 'store.dashboard',
            self::Rider => 'rider.dashboard',
            self::Admin => 'admin.dashboard',
        };
    }
}
