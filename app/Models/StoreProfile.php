<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Support\ImageUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class StoreProfile extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'store_name', 'description', 'logo_path', 'cover_path',
        'status', 'rejection_reason', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApprovalStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function requirements(): HasOne
    {
        return $this->hasOne(StoreRequirement::class);
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ApprovalStatus::Approved);
    }

    public function logoUrl(): string
    {
        return ImageUrl::for($this->logo_path, 'store_logo');
    }

    public function coverUrl(): string
    {
        return ImageUrl::for($this->cover_path, 'store_cover');
    }
}
