<?php

namespace App\Enums;

enum RiderAvailability: string
{
    case Available = 'available';
    case Unavailable = 'unavailable';
    case OnDelivery = 'on_delivery';
}
