<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Preparing = 'preparing';
    case ReadyForPickup = 'ready_for_pickup';
    case RiderAssigned = 'rider_assigned';
    case PickedUp = 'picked_up';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Accepted => 'Accepted',
            self::Rejected => 'Rejected',
            self::Preparing => 'Preparing',
            self::ReadyForPickup => 'Ready for Pickup',
            self::RiderAssigned => 'Rider Assigned',
            self::PickedUp => 'Picked Up',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Accepted => 'blue',
            self::Rejected => 'red',
            self::Preparing => 'indigo',
            self::ReadyForPickup => 'purple',
            self::RiderAssigned => 'cyan',
            self::PickedUp => 'orange',
            self::Delivered => 'green',
            self::Cancelled => 'gray',
        };
    }
}
