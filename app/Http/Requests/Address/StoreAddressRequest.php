<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:100'],
            'region_code' => ['required', 'string', 'max:10'],
            'province_code' => ['required', 'string', 'max:10'],
            'city_code' => ['required', 'string', 'max:10'],
            'barangay_code' => ['required', 'string', 'max:10'],
            'street_address' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}
