@php
    $value = fn (string $key) => old($key, data_get($address ?? null, $key));
@endphp

<div data-psgc-form data-address-form class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Label</label>
        <input type="text" name="label" value="{{ $value('label') }}" placeholder="Home, Work..." class="mt-1 w-full rounded-lg border-gray-300">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Region</label>
            <select name="region_code" data-psgc-region data-selected="{{ $value('region_code') }}" required class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">Select Region</option>
                @foreach ($regions as $region)
                    <option value="{{ data_get($region, 'code') }}" @selected($value('region_code') === data_get($region, 'code'))>{{ data_get($region, 'name') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Province / District</label>
            <select name="province_code" data-psgc-province data-selected="{{ $value('province_code') }}" required class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">Select Province / District</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">City / Municipality</label>
            <select name="city_code" data-psgc-city data-selected="{{ $value('city_code') }}" required class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">Select City/Municipality</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Barangay</label>
            <select name="barangay_code" data-psgc-barangay data-selected="{{ $value('barangay_code') }}" required class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">Select Barangay</option>
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Street Address</label>
        <input type="text" name="street_address" value="{{ $value('street_address') }}" required class="mt-1 w-full rounded-lg border-gray-300">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Postal Code</label>
            <input type="text" name="postal_code" value="{{ $value('postal_code') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Landmark</label>
            <input type="text" name="landmark" value="{{ $value('landmark') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </div>
    </div>

    <div class="hidden rounded-lg border border-brand-100 bg-brand-50/60 px-4 py-3 text-sm text-gray-700" data-detected-address-wrap>
        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-brand-600">Detected address</p>
        <p data-detected-address></p>
    </div>

    <div class="flex flex-wrap gap-4 items-center">
        <button type="button" data-use-location class="px-4 py-2 text-sm border border-brand-600 text-brand-600 rounded-lg hover:bg-brand-50">Use My Current Location</button>
        <input type="hidden" name="latitude" value="{{ $value('latitude') }}">
        <input type="hidden" name="longitude" value="{{ $value('longitude') }}">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_default" value="1" @checked($value('is_default')) class="rounded border-gray-300 text-brand-600">
            Set as default address
        </label>
    </div>

    <div data-address-map class="h-48 rounded-lg border border-gray-200 @if(! $value('latitude')) hidden @endif"></div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
@endpush
