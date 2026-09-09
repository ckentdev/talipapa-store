<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PsgcCity extends Model
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['code', 'province_code', 'name', 'type'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(PsgcProvince::class, 'province_code', 'code');
    }

    public function barangays(): HasMany
    {
        return $this->hasMany(PsgcBarangay::class, 'city_code', 'code');
    }
}
