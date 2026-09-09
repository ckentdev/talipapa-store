<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiderRequirement extends Model
{
    protected $fillable = [
        'rider_profile_id', 'valid_id_path', 'driver_license_path', 'notes',
    ];

    public function riderProfile(): BelongsTo
    {
        return $this->belongsTo(RiderProfile::class);
    }
}
