@props([
    'regions' => [],
    'address' => null,
    'showMap' => true,
    'showLabel' => true,
    'labelName' => 'label',
])

@php
    $fieldClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-brand-600 focus:ring-brand-600';
    $value = fn (string $key) => old($key, data_get($address, $key));
@endphp

<div {{ $attributes->merge(['class' => 'w-full space-y-4']) }} data-psgc-form data-address-form>
    <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-2">
        @if ($showLabel)
            <div>
                <label for="address-label" class="mb-1 block text-sm font-medium text-gray-700">Address Label</label>
                <input
                    type="text"
                    id="address-label"
                    name="{{ $labelName }}"
                    value="{{ old($labelName, data_get($address, $labelName)) }}"
                    placeholder="e.g. Home, Work"
                    class="{{ $fieldClass }}"
                >
            </div>
        @endif

        <div @class(['md:col-span-2' => ! $showLabel])>
            <label for="address-region" class="mb-1 block text-sm font-medium text-gray-700">Region</label>
            <select
                id="address-region"
                name="region_code"
                data-psgc-region
                data-selected="{{ $value('region_code') }}"
                required
                class="{{ $fieldClass }}"
            >
                <option value="">Select Region</option>
                @foreach ($regions as $region)
                    <option
                        value="{{ data_get($region, 'code') }}"
                        @selected($value('region_code') == data_get($region, 'code'))
                    >
                        {{ data_get($region, 'name') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="address-province" class="mb-1 block text-sm font-medium text-gray-700">Province / District</label>
            <select
                id="address-province"
                name="province_code"
                data-psgc-province
                data-selected="{{ $value('province_code') }}"
                required
                class="{{ $fieldClass }}"
            >
                <option value="">Select Province / District</option>
            </select>
        </div>

        <div>
            <label for="address-city" class="mb-1 block text-sm font-medium text-gray-700">City / Municipality</label>
            <select
                id="address-city"
                name="city_code"
                data-psgc-city
                data-selected="{{ $value('city_code') }}"
                required
                class="{{ $fieldClass }}"
            >
                <option value="">Select City/Municipality</option>
            </select>
        </div>

        <div>
            <label for="address-barangay" class="mb-1 block text-sm font-medium text-gray-700">Barangay</label>
            <select
                id="address-barangay"
                name="barangay_code"
                data-psgc-barangay
                data-selected="{{ $value('barangay_code') }}"
                required
                class="{{ $fieldClass }}"
            >
                <option value="">Select Barangay</option>
            </select>
        </div>

        <div>
            <label for="address-street" class="mb-1 block text-sm font-medium text-gray-700">Street Address</label>
            <input
                type="text"
                id="address-street"
                name="street_address"
                value="{{ $value('street_address') }}"
                placeholder="House no., street, subdivision"
                required
                class="{{ $fieldClass }}"
            >
        </div>

        <div>
            <label for="address-postal" class="mb-1 block text-sm font-medium text-gray-700">Postal Code</label>
            <input
                type="text"
                id="address-postal"
                name="postal_code"
                value="{{ $value('postal_code') }}"
                placeholder="e.g. 1000"
                class="{{ $fieldClass }}"
            >
        </div>

        <div>
            <label for="address-landmark" class="mb-1 block text-sm font-medium text-gray-700">Landmark</label>
            <input
                type="text"
                id="address-landmark"
                name="landmark"
                value="{{ $value('landmark') }}"
                placeholder="Near church, school, etc."
                class="{{ $fieldClass }}"
            >
        </div>
    </div>

    <input type="hidden" name="latitude" value="{{ $value('latitude') }}">
    <input type="hidden" name="longitude" value="{{ $value('longitude') }}">

    <div class="hidden rounded-lg border border-brand-100 bg-brand-50/60 px-4 py-3 text-sm text-gray-700" data-detected-address-wrap>
        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-brand-600">Detected address</p>
        <p data-detected-address></p>
    </div>

    <div class="flex w-full flex-col gap-3 md:flex-row md:items-center">
        <button
            type="button"
            data-use-location
            class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-brand-600 bg-white px-4 py-2.5 text-sm font-medium text-brand-600 hover:bg-brand-50 focus:outline-none focus:ring-2 focus:ring-brand-600 md:w-auto"
        >
            <i class="ri-map-pin-user-line text-base" aria-hidden="true"></i>
            Use My Current Location
        </button>
        <p class="text-xs text-gray-500">Pin your exact location for faster delivery.</p>
    </div>

    @if ($showMap)
        <div
            data-address-map
            @class([
                'h-56 w-full overflow-hidden rounded-lg border border-gray-200 md:h-72',
                'hidden' => ! $value('latitude'),
            ])
        ></div>
    @endif
</div>
