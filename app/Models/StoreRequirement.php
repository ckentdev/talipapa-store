<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreRequirement extends Model
{
    protected $fillable = [
        'store_profile_id', 'business_permit_path', 'valid_id_path', 'notes',
    ];

    public function storeProfile(): BelongsTo
    {
        return $this->belongsTo(StoreProfile::class);
    }
}
