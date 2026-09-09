<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasPushSubscriptions;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'status',
        'sound_alerts_enabled',
        'location_permission',
        'microphone_permission',
        'push_permission',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
            'sound_alerts_enabled' => 'boolean',
            'location_permission' => 'boolean',
            'microphone_permission' => 'boolean',
            'push_permission' => 'boolean',
        ];
    }

    public function isCustomer(): bool
    {
        return $this->role === UserRole::Customer;
    }

    public function isStoreOwner(): bool
    {
        return $this->role === UserRole::StoreOwner;
    }

    public function isRider(): bool
    {
        return $this->role === UserRole::Rider;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function customerProfile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function storeProfile(): HasOne
    {
        return $this->hasOne(StoreProfile::class);
    }

    public function riderProfile(): HasOne
    {
        return $this->hasOne(RiderProfile::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function riderOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'rider_id');
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function vocUtterances(): HasMany
    {
        return $this->hasMany(VocUtterance::class);
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
