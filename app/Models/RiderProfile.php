<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Enums\RiderAvailability;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RiderProfile extends Model
{
    protected $fillable = [
        'user_id', 'vehicle_type', 'plate_number', 'status',
        'availability', 'rejection_reason', 'approved_at',
        'latitude', 'longitude',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApprovalStatus::class,
            'availability' => RiderAvailability::class,
            'approved_at' => 'datetime',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requirements(): HasOne
    {
        return $this->hasOne(RiderRequirement::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(RiderLocation::class);
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ApprovalStatus::Approved);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('availability', RiderAvailability::Available);
    }
}
