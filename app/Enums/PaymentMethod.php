<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cod = 'cod';
    case Cash = 'cash';
    case EWallet = 'ewallet';
    case Card = 'card';

    public function label(): string
    {
        return match ($this) {
            self::Cod => 'Cash on Delivery (COD)',
            self::Cash => 'Cash Payment for Walk-in Customers',
            self::EWallet => 'e-Wallet',
            self::Card => 'Credit / Debit Card',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Cod => 'Pay with cash when your order arrives.',
            self::Cash => 'Accept cash payment directly at your store counter.',
            self::EWallet => 'Pay using GCash, Maya, GrabPay, ShopeePay, and other e-wallets.',
            self::Card => 'Pay with Visa, Mastercard, or other supported cards.',
        };
    }

    public function isIntegrated(): bool
    {
        return in_array($this, [self::Cod, self::Cash], true);
    }

    /**
     * @return list<self>
     */
    public static function forPos(): array
    {
        return [
            self::Cash,
            self::EWallet,
        ];
    }

    /**
     * @return list<self>
     */
    public static function forOnlineCheckout(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $method): bool => $method !== self::Cash,
        ));
    }

    public static function tryFromStored(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        if ($value === 'gcash') {
            return self::EWallet;
        }

        return self::tryFrom($value);
    }
}
