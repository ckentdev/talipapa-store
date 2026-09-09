<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'addressable_type', 'addressable_id', 'label',
        'region_code', 'province_code', 'city_code', 'barangay_code',
        'street_address', 'postal_code', 'landmark',
        'latitude', 'longitude', 'is_default',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(PsgcRegion::class, 'region_code', 'code');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(PsgcProvince::class, 'province_code', 'code');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(PsgcCity::class, 'city_code', 'code');
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(PsgcBarangay::class, 'barangay_code', 'code');
    }

    public function fullAddress(): string
    {
        $parts = array_filter([
            $this->street_address,
            $this->barangay?->name,
            $this->city?->name,
            $this->province?->name,
            $this->region?->name,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }
}
