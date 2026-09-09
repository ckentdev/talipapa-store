@extends('layouts.store')

@section('title', 'Store Information')

@section('content')
@php
    use App\Enums\ApprovalStatus;

    $inputClass = 'block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-base placeholder:text-gray-400 focus:border-brand-600 focus:ring-brand-600';
    $labelClass = 'mb-1 block text-base font-medium text-gray-700';

    $statusClasses = match ($store?->status) {
        ApprovalStatus::Approved => 'bg-green-100 text-green-800',
        ApprovalStatus::Pending => 'bg-yellow-100 text-yellow-800',
        ApprovalStatus::Rejected => 'bg-red-100 text-red-800',
        ApprovalStatus::Suspended => 'bg-orange-100 text-orange-800',
        default => 'bg-gray-100 text-gray-800',
    };

    $address = $store?->addresses->first();
    $requirements = $store?->requirements;
    $isApproved = $isApproved ?? false;

    $addressErrorFields = [
        'region_code', 'province_code', 'city_code', 'barangay_code',
        'street_address', 'postal_code', 'landmark', 'latitude', 'longitude',
    ];
    $detailsErrorFields = ['store_name', 'description', 'logo', 'cover'];

    $activeTab = collect($addressErrorFields)->contains(fn ($field) => $errors->has($field)) ? 'address' : 'details';
    if (collect($detailsErrorFields)->contains(fn ($field) => $errors->has($field))) {
        $activeTab = 'details';
    }

    $tabBtnClass = fn (string $tab) => $activeTab === $tab
        ? 'inline-flex items-center gap-2 border-b-2 border-forest-600 px-3 py-2.5 text-base font-semibold text-forest-700 sm:px-4 sm:py-3'
        : 'inline-flex items-center gap-2 border-b-2 border-transparent px-3 py-2.5 text-base font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 sm:px-4 sm:py-3';
@endphp

@if ($store && $store->status !== ApprovalStatus::Approved)
    <x-alert type="warning" title="Store under review" class="mb-6">
        Your store is <strong>{{ $store->status->value }}</strong>.
        @if ($store->rejection_reason)
            Reason: {{ $store->rejection_reason }}
        @else
            You will be notified once an admin reviews your application.
        @endif
    </x-alert>
@endif

<div class="w-full">
    <div class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-4 py-3 sm:px-5">
            <div class="flex min-w-0 items-center gap-3">
                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-forest-600/10 text-forest-600">
                    <i class="ri-store-2-line text-lg" aria-hidden="true"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="truncate text-base font-bold text-gray-900">Store Information</h2>
                    <p class="truncate text-base text-gray-500">Update your store details and location.</p>
                </div>
            </div>
            <a href="{{ route('store.dashboard') }}" class="inline-flex shrink-0 items-center gap-1 text-base font-medium text-gray-500 hover:text-brand-600">
                <i class="ri-arrow-left-line" aria-hidden="true"></i>
                Dashboard
            </a>
        </div>

        @if ($store)
            <form
                id="store-update-form"
                method="POST"
                action="{{ route('store.information.update') }}"
                enctype="multipart/form-data"
                class="flex flex-col"
                data-confirm-on-submit
                data-confirm-title="Save changes"
                data-confirm-message="Update your store information?"
                data-confirm-label="Save"
                data-confirm-variant="primary"
            >
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-5 p-4 md:grid-cols-3 md:p-5">
                    <aside class="md:col-span-1">
                        @include('store.partials._store-preview-sidebar', compact('store', 'user', 'statusClasses', 'address', 'requirements'))
                    </aside>

                    <div class="md:col-span-2">
                        <div class="border-b border-gray-200">
                            <ul
                                class="-mb-px flex flex-wrap gap-1"
                                id="store-information-tabs"
                                data-tabs-toggle="#store-information-tab-content"
                                role="tablist"
                            >
                                <li role="presentation">
                                    <button
                                        type="button"
                                        id="details-tab"
                                        data-tabs-target="#store-details-panel"
                                        role="tab"
                                        aria-controls="store-details-panel"
                                        aria-selected="{{ $activeTab === 'details' ? 'true' : 'false' }}"
                                        class="{{ $tabBtnClass('details') }}"
                                    >
                                        <i class="ri-file-list-3-line text-base" aria-hidden="true"></i>
                                        Details
                                    </button>
                                </li>
                                <li role="presentation">
                                    <button
                                        type="button"
                                        id="address-tab"
                                        data-tabs-target="#address-panel"
                                        role="tab"
                                        aria-controls="address-panel"
                                        aria-selected="{{ $activeTab === 'address' ? 'true' : 'false' }}"
                                        class="{{ $tabBtnClass('address') }}"
                                    >
                                        <i class="ri-map-pin-2-line text-base" aria-hidden="true"></i>
                                        Address
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div id="store-information-tab-content" class="mt-4">
                            <div
                                id="store-details-panel"
                                role="tabpanel"
                                aria-labelledby="details-tab"
                                @class(['space-y-4', 'hidden' => $activeTab !== 'details'])
                            >
                                <p class="text-base text-gray-500">How customers see your store.</p>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <label for="store_name" class="{{ $labelClass }}">Store name</label>
                                        <input
                                            id="store_name"
                                            type="text"
                                            name="store_name"
                                            value="{{ old('store_name', $store->store_name) }}"
                                            required
                                            placeholder="e.g. Fresh Farm Produce"
                                            class="{{ $inputClass }}"
                                        >
                                        <x-input-error for="store_name" class="mt-1" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="description" class="{{ $labelClass }}">Description</label>
                                        <textarea
                                            id="description"
                                            name="description"
                                            rows="3"
                                            placeholder="Tell customers what you sell..."
                                            @disabled(! $isApproved)
                                            @class([$inputClass, 'bg-gray-50 text-gray-500' => ! $isApproved])
                                        >{{ old('description', $store->description) }}</textarea>
                                        <x-input-error for="description" class="mt-1" />
                                    </div>

                                    @if ($isApproved)
                                        <div>
                                            <label for="logo" class="{{ $labelClass }}">Logo</label>
                                            <x-file-input
                                                id="logo"
                                                name="logo"
                                                accept=".jpg,.jpeg,.png"
                                                :preview="true"
                                                placeholder="{{ filled($store->logo_path) ? 'Current logo uploaded' : 'No logo uploaded' }}"
                                                hint="JPG or PNG, maximum 5MB."
                                            />
                                            <x-input-error for="logo" class="mt-1" />
                                        </div>

                                        <div>
                                            <label for="cover" class="{{ $labelClass }}">Cover photo</label>
                                            <x-file-input
                                                id="cover"
                                                name="cover"
                                                accept=".jpg,.jpeg,.png"
                                                :preview="true"
                                                placeholder="{{ filled($store->cover_path) ? 'Current cover uploaded' : 'No cover uploaded' }}"
                                                hint="JPG or PNG, maximum 5MB."
                                            />
                                            <x-input-error for="cover" class="mt-1" />
                                        </div>
                                    @endif
                                </div>

                                @if (! $isApproved)
                                    <p class="flex items-start gap-2 rounded-lg border border-amber-100 bg-amber-50 px-3 py-2.5 text-base text-amber-800">
                                        <i class="ri-lock-line mt-0.5 shrink-0" aria-hidden="true"></i>
                                        Description, logo, and cover photo can be updated once your store is approved.
                                    </p>
                                @endif
                            </div>

                            <div
                                id="address-panel"
                                role="tabpanel"
                                aria-labelledby="address-tab"
                                @class(['space-y-4', 'hidden' => $activeTab !== 'address'])
                            >
                                @if ($isApproved)
                                    <p class="text-base text-gray-500">Where customers and riders can find your store.</p>

                                    <p class="rounded-lg bg-avocado-50/60 px-3 py-2.5 text-base text-gray-600">
                                        This address is shown to customers as your store location.
                                    </p>

                                    <x-address-form
                                        :regions="$regions"
                                        :address="$address"
                                        :show-label="false"
                                        class="[&_label]:text-base [&_input]:text-base [&_select]:text-base"
                                    />

                                    <x-input-error for="region_code" class="mt-1" />
                                    <x-input-error for="province_code" class="mt-1" />
                                    <x-input-error for="city_code" class="mt-1" />
                                    <x-input-error for="barangay_code" class="mt-1" />
                                    <x-input-error for="street_address" class="mt-1" />
                                @else
                                    <p class="flex items-start gap-2 rounded-lg border border-amber-100 bg-amber-50 px-3 py-2.5 text-base text-amber-800">
                                        <i class="ri-lock-line mt-0.5 shrink-0" aria-hidden="true"></i>
                                        Store address can be updated once your store is approved.
                                    </p>

                                    @if ($address)
                                        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                                            <div class="space-y-2 p-3 text-base text-gray-600">
                                                <p>{{ $address->fullAddress() }}</p>
                                                @if ($address->landmark)
                                                    <p class="flex items-start gap-1.5 text-gray-500">
                                                        <i class="ri-signpost-line mt-0.5 shrink-0" aria-hidden="true"></i>
                                                        {{ $address->landmark }}
                                                    </p>
                                                @endif
                                                @if ($address->latitude && $address->longitude)
                                                    <p class="flex items-center gap-1.5 text-base text-gray-500">
                                                        <i class="ri-map-pin-user-line text-brand-600" aria-hidden="true"></i>
                                                        Location pinned on map
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <x-page-card.footer>
                    <a href="{{ route('store.dashboard') }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50">
                        <i class="ri-close-line" aria-hidden="true"></i>
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-forest-600 px-3 py-2 text-base font-semibold text-white hover:bg-forest-700">
                        <i class="ri-save-line" aria-hidden="true"></i>
                        Save changes
                    </button>
                </x-page-card.footer>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if ($activeTab === 'address')
            document.getElementById('address-tab')?.click();
        @endif

        document.getElementById('address-tab')?.addEventListener('click', () => {
            setTimeout(() => {
                document.querySelectorAll('[data-address-map]').forEach((mapEl) => {
                    if (mapEl._map) {
                        mapEl._map.invalidateSize();
                    }
                });
            }, 150);
        });
    });
</script>
@endpush
