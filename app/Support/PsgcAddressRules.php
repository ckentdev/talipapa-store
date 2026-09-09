<?php

namespace App\Support;

class PsgcAddressRules
{
    /**
     * @return array<string, mixed>
     */
    public static function validation(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:50'],
            'region_code' => ['required', 'string', 'regex:/^\d{9}$/'],
            'province_code' => ['required', 'string', 'regex:/^\d{9}$/'],
            'city_code' => ['required', 'string', 'regex:/^\d{9}$/'],
            'barangay_code' => ['required', 'string', 'regex:/^\d{9}$/'],
            'street_address' => ['required', 'string', 'max:500'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ];
    }
}
