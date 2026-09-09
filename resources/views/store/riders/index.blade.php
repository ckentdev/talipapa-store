@extends('layouts.store')

@section('title', 'Available Riders')

@section('content')
@php
    $kpiCards = [
        [
            'label' => 'Available Now',
            'value' => $riderStats['total'],
            'icon' => 'ri-user-star-line',
            'iconBg' => 'bg-green-100 text-green-700',
            'valueClass' => 'text-green-700',
            'hint' => 'Ready for delivery',
        ],
        [
            'label' => 'GPS Active',
            'value' => $riderStats['with_gps'],
            'icon' => 'ri-map-pin-user-line',
            'iconBg' => 'bg-blue-100 text-blue-700',
            'valueClass' => 'text-blue-700',
            'hint' => 'Sharing live location',
        ],
        [
            'label' => 'Motorcycle',
            'value' => $riderStats['motorcycle'],
            'icon' => 'ri-motorbike-line',
            'iconBg' => 'bg-forest-100 text-forest-700',
            'valueClass' => 'text-gray-900',
            'hint' => 'Two-wheel riders',
        ],
        [
            'label' => 'Other Vehicles',
            'value' => $riderStats['other_vehicles'],
            'icon' => 'ri-truck-line',
            'iconBg' => 'bg-brand-100 text-brand-700',
            'valueClass' => 'text-brand-700',
            'hint' => 'Bicycle, tricycle, car',
        ],
    ];

    $vehicleIcon = fn (?string $type) => match (strtolower($type ?? '')) {
        'motorcycle' => 'ri-motorbike-line',
        'bicycle' => 'ri-riding-line',
        'tricycle' => 'ri-e-bike-2-line',
        'car' => 'ri-car-line',
        default => 'ri-truck-line',
    };
@endphp

{{-- Overview --}}
<section class="mb-6">
    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Rider overview</h2>
            <p class="text-base text-gray-500">Approved riders online and ready to deliver</p>
        </div>
        <a
            href="{{ route('store.orders') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-base font-medium text-gray-700 shadow-sm transition hover:border-brand-200 hover:text-brand-700"
        >
            <i class="ri-shopping-bag-3-line text-lg" aria-hidden="true"></i>
            Assign from orders
        </a>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        @foreach ($kpiCards as $card)
            <article class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-avocado-200 hover:shadow-md sm:p-5">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-base font-medium text-gray-500">{{ $card['label'] }}</p>
                        <p @class(['mt-1.5 text-base font-bold tracking-tight', $card['valueClass']])>{{ $card['value'] }}</p>
                        <p class="mt-0.5 text-base text-gray-500">{{ $card['hint'] }}</p>
                    </div>
                    <span @class(['inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl sm:h-10 sm:w-10', $card['iconBg']])>
                        <i class="{{ $card['icon'] }} text-lg" aria-hidden="true"></i>
                    </span>
                </div>
            </article>
        @endforeach
    </div>
</section>

@if ($riders->isEmpty())
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        @include('layouts.partials._empty_state', [
            'title' => 'No riders available',
            'message' => 'There are no approved riders online right now. Check back later or assign a rider when one becomes available.',
            'icon' => '<i class="ri-user-search-line text-2xl" aria-hidden="true"></i>',
        ])
    </div>
@else
    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 bg-gradient-to-r from-avocado-50/50 to-white px-5 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-forest-600/10 text-forest-600">
                    <i class="ri-team-line text-lg" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="font-bold text-gray-900">Online riders</h2>
                    <p class="text-base text-gray-500">{{ $riders->count() }} ready to accept deliveries</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 sm:p-5 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($riders as $rider)
                @php
                    $profile = $rider->riderProfile;
                    $hasGps = $profile?->latitude && $profile?->longitude;
                @endphp
                <article class="flex flex-col rounded-xl border border-gray-200 bg-white p-4 transition hover:border-avocado-200 hover:shadow-md">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-forest-600 to-brand-600 text-base font-bold text-white">
                            {{ strtoupper(substr($rider->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="truncate text-base font-semibold text-gray-900">{{ $rider->name }}</h3>
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                    Available
                                </span>
                            </div>
                            @if ($rider->phone)
                                <a href="tel:{{ $rider->phone }}" class="mt-1 inline-flex items-center gap-1 truncate text-base text-brand-600 hover:text-brand-700">
                                    <i class="ri-phone-line shrink-0" aria-hidden="true"></i>
                                    {{ $rider->phone }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <dl class="mt-4 space-y-2 border-t border-gray-100 pt-3 text-base">
                        <div class="flex items-center justify-between gap-2">
                            <dt class="flex items-center gap-1.5 text-gray-500">
                                <i class="{{ $vehicleIcon($profile?->vehicle_type) }}" aria-hidden="true"></i>
                                Vehicle
                            </dt>
                            <dd class="truncate font-medium text-gray-900">{{ $profile?->vehicle_type ?? '—' }}</dd>
                        </div>
                        @if ($profile?->plate_number)
                            <div class="flex items-center justify-between gap-2">
                                <dt class="flex items-center gap-1.5 text-gray-500">
                                    <i class="ri-barcode-line" aria-hidden="true"></i>
                                    Plate
                                </dt>
                                <dd class="truncate font-medium text-gray-900">{{ $profile->plate_number }}</dd>
                            </div>
                        @endif
                        <div class="flex items-center justify-between gap-2">
                            <dt class="flex items-center gap-1.5 text-gray-500">
                                <i class="ri-map-pin-2-line" aria-hidden="true"></i>
                                Location
                            </dt>
                            <dd @class(['font-medium', 'text-green-700' => $hasGps, 'text-gray-400' => ! $hasGps])>
                                {{ $hasGps ? 'GPS active' : 'Not shared' }}
                            </dd>
                        </div>
                    </dl>
                </article>
            @endforeach
        </div>
    </section>

    <p class="mt-4 flex items-start gap-2 rounded-xl border border-avocado-200 bg-avocado-50/60 px-4 py-3 text-base text-gray-600">
        <i class="ri-information-line mt-0.5 shrink-0 text-lg text-forest-600" aria-hidden="true"></i>
        To assign a rider, open an order marked ready and select a rider from the delivery assignment dropdown.
    </p>
@endif
@endsection
