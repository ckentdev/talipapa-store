<?php

namespace App\Support;

use App\Enums\EWalletProvider;
use App\Enums\PaymentMethod;

class CheckoutPaymentSummary
{
    /**
     * @param  array<string, mixed>  $checkout
     */
    public static function label(array $checkout): string
    {
        $method = PaymentMethod::tryFromStored($checkout['payment_method'] ?? PaymentMethod::Cod->value)
            ?? PaymentMethod::Cod;

        return match ($method) {
            PaymentMethod::EWallet => self::eWalletLabel($checkout),
            PaymentMethod::Card => self::cardLabel($checkout),
            default => $method->label(),
        };
    }

    /**
     * @param  array<string, mixed>  $checkout
     */
    public static function orderNote(array $checkout): ?string
    {
        $method = PaymentMethod::tryFromStored($checkout['payment_method'] ?? null);

        if (! $method || in_array($method, [PaymentMethod::Cod, PaymentMethod::Cash], true)) {
            return null;
        }

        return match ($method) {
            PaymentMethod::EWallet => 'e-Wallet: '.(EWalletProvider::tryFromStored($checkout['ewallet_provider'] ?? null)?->label() ?? 'Not specified'),
            PaymentMethod::Card => 'Card: '.($checkout['payment']['holder'] ?? 'Cardholder').' •••• '.($checkout['payment']['last_four'] ?? '????'),
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $checkout
     */
    private static function eWalletLabel(array $checkout): string
    {
        $provider = EWalletProvider::tryFromStored($checkout['ewallet_provider'] ?? null);

        return $provider
            ? PaymentMethod::EWallet->label().' — '.$provider->label()
            : PaymentMethod::EWallet->label();
    }

    /**
     * @param  array<string, mixed>  $checkout
     */
    private static function cardLabel(array $checkout): string
    {
        $lastFour = $checkout['payment']['last_four'] ?? null;

        return $lastFour
            ? PaymentMethod::Card->label().' ending in '.$lastFour
            : PaymentMethod::Card->label();
    }
}
