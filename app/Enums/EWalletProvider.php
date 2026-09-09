<?php

namespace App\Enums;

enum EWalletProvider: string
{
    case Gcash = 'gcash';
    case Maya = 'maya';
    case GrabPay = 'grabpay';
    case ShopeePay = 'shopeepay';
    case Coins = 'coins';

    public function label(): string
    {
        return match ($this) {
            self::Gcash => 'GCash',
            self::Maya => 'Maya',
            self::GrabPay => 'GrabPay',
            self::ShopeePay => 'ShopeePay',
            self::Coins => 'Coins.ph',
        };
    }

    public function logoBasename(): string
    {
        return match ($this) {
            self::Coins => 'coinph',
            default => $this->value,
        };
    }

    public function logoUrl(): string
    {
        $basename = $this->logoBasename();
        $webpPath = public_path("img/payments/{$basename}.webp");

        if (is_file($webpPath)) {
            return asset("img/payments/{$basename}.webp");
        }

        return asset("img/payments/{$this->value}.svg");
    }

    public function cardClass(): string
    {
        return match ($this) {
            self::Gcash => 'border-blue-100 bg-blue-50/40 hover:border-blue-300 has-[:checked]:border-blue-500 has-[:checked]:ring-blue-200',
            self::Maya => 'border-green-100 bg-green-50/40 hover:border-green-300 has-[:checked]:border-green-500 has-[:checked]:ring-green-200',
            self::GrabPay => 'border-emerald-100 bg-emerald-50/40 hover:border-emerald-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-emerald-200',
            self::ShopeePay => 'border-orange-100 bg-orange-50/40 hover:border-orange-300 has-[:checked]:border-orange-500 has-[:checked]:ring-orange-200',
            self::Coins => 'border-yellow-100 bg-yellow-50/40 hover:border-yellow-300 has-[:checked]:border-yellow-500 has-[:checked]:ring-yellow-200',
        };
    }

    public function accentClass(): string
    {
        return match ($this) {
            self::Gcash => 'text-blue-600',
            self::Maya => 'text-green-600',
            self::GrabPay => 'text-emerald-600',
            self::ShopeePay => 'text-orange-600',
            self::Coins => 'text-yellow-600',
        };
    }

    public static function tryFromStored(?string $value): ?self
    {
        return $value ? self::tryFrom($value) : null;
    }
}
