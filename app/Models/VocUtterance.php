<?php

namespace App\Models;

use App\Enums\VocSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VocUtterance extends Model
{
    protected $fillable = [
        'user_id',
        'source',
        'original_text',
        'language',
        'intent',
        'result_count',
    ];

    protected function casts(): array
    {
        return [
            'source' => VocSource::class,
            'result_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(VocAttribute::class);
    }
}
