<?php

namespace App\Enums;

enum StoreStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Suspended = 'suspended';
}
