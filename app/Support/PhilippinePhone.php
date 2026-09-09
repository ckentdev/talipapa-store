<?php

namespace App\Support;

class PhilippinePhone
{
    public const PREFIX = '+63';

    public static function isValid(?string $phone): bool
    {
        return self::normalize($phone) !== null;
    }

    public static function normalize(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($digits, '63')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (! preg_match('/^9\d{9}$/', $digits)) {
            return null;
        }

        return self::PREFIX.$digits;
    }

    public static function localPart(?string $phone): string
    {
        if ($phone === null || trim($phone) === '') {
            return '';
        }

        $normalized = self::normalize($phone);

        if ($normalized !== null) {
            return substr($normalized, strlen(self::PREFIX));
        }

        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($digits, '63')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return $digits;
    }
}
