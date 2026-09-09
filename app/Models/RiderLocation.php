<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiderLocation extends Model
{
    protected $fillable = [
        'rider_profile_id', 'latitude', 'longitude', 'accuracy', 'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'accuracy' => 'decimal:2',
            'recorded_at' => 'datetime',
        ];
    }

    public function riderProfile(): BelongsTo
    {
        return $this->belongsTo(RiderProfile::class);
    }
}
