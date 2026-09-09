<?php

namespace App\Models;

use App\Support\ImageUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_profile_id', 'category_id', 'name', 'barcode', 'description',
        'price', 'stock', 'image_path', 'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(StoreProfile::class, 'store_profile_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true)->where('stock', '>', 0);
    }

    public function imageUrl(): string
    {
        return ImageUrl::for($this->image_path, 'product');
    }
}
