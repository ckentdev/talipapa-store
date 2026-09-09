<?php

namespace App\Models;

use App\Casts\PaymentMethodCast;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id', 'store_profile_id', 'rider_id',
        'address_id', 'status', 'payment_method', 'subtotal',
        'delivery_fee', 'total', 'notes', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_method' => PaymentMethodCast::class,
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(StoreProfile::class, 'store_profile_id');
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function isPosSale(): bool
    {
        return str_starts_with($this->notes ?? '', '[POS]');
    }

    public function displayCustomerName(): string
    {
        if ($this->isPosSale()) {
            if (preg_match('/Customer:\s*(.+)$/m', $this->notes ?? '', $matches)) {
                return trim($matches[1]);
            }

            return 'Walk-in customer';
        }

        return $this->customer?->name ?? 'Guest customer';
    }
}
