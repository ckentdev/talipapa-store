<?php

namespace App\Models;

use App\Enums\VocAttributeType;
use App\Enums\VocPolarity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocAttribute extends Model
{
    protected $fillable = [
        'voc_utterance_id',
        'type',
        'value',
        'polarity',
    ];

    protected function casts(): array
    {
        return [
            'type' => VocAttributeType::class,
            'polarity' => VocPolarity::class,
        ];
    }

    public function utterance(): BelongsTo
    {
        return $this->belongsTo(VocUtterance::class, 'voc_utterance_id');
    }
}
