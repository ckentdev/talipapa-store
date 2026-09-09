@extends('layouts.guest')

@section('title', 'Register as Rider')

@section('content')
<div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
    <div class="mb-8 text-center">
        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-brand-600">Rider</p>
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Register as a delivery rider</h1>
        <p class="mx-auto mt-2 max-w-2xl text-sm text-gray-600 sm:text-base">Complete all steps to submit your rider application for approval.</p>
    </div>

    @include('auth.partials.registration-stepper', [
        'step' => $step,
        'steps' => $steps,
        'accent' => 'forest',
        'icons' => [
            1 => 'ri-user-3-line',
            2 => 'ri-riding-line',
            3 => 'ri-map-pin-2-line',
            4 => 'ri-file-upload-line',
            5 => 'ri-file-list-3-line',
        ],
    ])

    <div class="mb-4">
        @include('layouts.partials._flash_alerts')
    </div>

    @php
        $stepMeta = [
            1 => ['icon' => 'ri-user-3-line', 'title' => 'Account Information', 'subtitle' => 'Your login details and contact number.'],
            2 => ['icon' => 'ri-riding-line', 'title' => 'Vehicle Information', 'subtitle' => 'Tell us what you use for deliveries.'],
            3 => ['icon' => 'ri-map-pin-2-line', 'title' => 'Home Address', 'subtitle' => 'Your primary address for verification and contact.'],
            4 => ['icon' => 'ri-file-upload-line', 'title' => 'Required Documents', 'subtitle' => 'Upload valid ID and driver\'s license for admin approval.'],
            5 => ['icon' => 'ri-file-list-3-line', 'title' => 'Review & Submit', 'subtitle' => 'Confirm everything before submitting your application.'],
        ];
        $current = $stepMeta[$step];
        $inputClass = 'block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm placeholder:text-gray-400 focus:border-brand-600 focus:ring-brand-600';
        $vehicleOptions = [
            'Motorcycle' => ['ri-motorbike-line', 'Fast deliveries on two wheels'],
            'Bicycle' => ['ri-riding-line', 'Eco-friendly local deliveries'],
            'Tricycle' => ['ri-e-bike-2-line', 'Neighborhood routes and short hauls'],
            'Car' => ['ri-car-line', 'Bulk or longer-distance deliveries'],
        ];
        $selectedVehicle = old('vehicle_type', $data['vehicle_type'] ?? '');
    @endphp

    <div class="w-full overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-6 py-5 sm:px-8">
            <div class="flex items-start gap-4">
                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-forest-600/10 text-forest-600">
                    <i class="{{ $current['icon'] }} text-xl" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 sm:text-xl">{{ $current['title'] }}</h2>
                    <p class="mt-0.5 text-sm text-gray-600">{{ $current['subtitle'] }}</p>
                </div>
            </div>
        </div>

        <div class="w-full p-6 sm:p-8 !pt-2">
        @if ($step === 1)
            <form method="POST" action="{{ route('register.rider.step', 1) }}" class="w-full space-y-6">
                @csrf
                <div class="grid w-full grid-cols-1 gap-x-6 gap-y-4 md:grid-cols-12">
                    <div class="md:col-span-6">
                        <label class="mb-1 block text-sm font-medium">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $data['name'] ?? '') }}" required autofocus placeholder="e.g. Khris Macatol Hibaya" class="{{ $inputClass }}">
                    </div>
                    <div class="md:col-span-3">
                        <label class="mb-1 block text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $data['email'] ?? '') }}" required placeholder="e.g. you@example.com" autocomplete="email" class="{{ $inputClass }}">
                    </div>
                    <div class="md:col-span-3">
                        <label class="mb-1 block text-sm font-medium">Phone</label>
                        <div class="flex overflow-hidden rounded-lg border border-gray-300 focus-within:border-brand-600 focus-within:ring-1 focus-within:ring-brand-600">
                            <span class="inline-flex shrink-0 items-center border-r border-gray-300 bg-gray-50 px-3 text-sm font-medium text-gray-600">{{ \App\Support\PhilippinePhone::PREFIX }}</span>
                            <input
                                type="tel"
                                name="phone"
                                value="{{ old('phone', \App\Support\PhilippinePhone::localPart($data['phone'] ?? '')) }}"
                                required
                                inputmode="numeric"
                                autocomplete="tel-national"
                                placeholder="9XX XXX XXXX"
                                maxlength="10"
                                pattern="9[0-9]{9}"
                                class="block w-full min-w-0 border-0 px-3 py-2.5 text-sm placeholder:text-gray-400 focus:ring-0"
                            >
                        </div>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid w-full grid-cols-1 gap-x-6 gap-y-4 md:grid-cols-2" data-password-strength>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Password</label>
                        <input type="password" name="password" data-password-input required placeholder="Create a strong password" autocomplete="new-password" class="{{ $inputClass }}">
                        <div class="mt-3 space-y-3 rounded-lg border border-gray-100 bg-gray-50/60 p-4" data-password-strength-panel>
                            <div class="flex gap-1" aria-hidden="true">
                                @for ($i = 0; $i < 10; $i++)
                                    <span class="h-2.5 flex-1 rounded-sm bg-brand-100 transition-colors duration-200" data-strength-segment></span>
                                @endfor
                            </div>
                            <p class="text-sm text-gray-700">
                                Level: <span class="font-semibold text-gray-900" data-strength-label>Empty</span>
                            </p>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Your password must contain:</p>
                                <ul class="mt-2 space-y-1.5">
                                    @foreach (\App\Support\PasswordStrength::hints() as $key => $hint)
                                        <li data-hint="{{ $key }}" class="flex items-center gap-2 text-sm text-gray-500">
                                            <i data-hint-icon class="ri-close-line text-sm text-gray-400" aria-hidden="true"></i>
                                            <span data-hint-text>{{ $hint['label'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-col">
                        <label class="mb-1 block text-sm font-medium">Confirm Password</label>
                        <input type="password" name="password_confirmation" data-password-confirm required placeholder="Re-enter your password" autocomplete="new-password" class="{{ $inputClass }}">
                        <div class="mt-3 hidden" data-password-match-panel>
                            <p class="flex items-center gap-2 text-sm font-medium text-gray-500" data-password-match-label></p>
                        </div>
                        <button type="submit" data-continue-button disabled class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-forest-600 px-4 py-3 text-sm font-semibold text-white hover:bg-forest-700 disabled:cursor-not-allowed disabled:opacity-50">
                            Continue
                            <i class="ri-arrow-right-line text-lg" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </form>
        @elseif ($step === 2)
            <form method="POST" action="{{ route('register.rider.step', 2) }}" class="w-full space-y-6">
                @csrf
                <div class="grid w-full grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach ($vehicleOptions as $type => [$icon, $description])
                        <label @class([
                            'flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition hover:border-gray-300',
                            'border-forest-600 bg-forest-600/5 ring-1 ring-forest-600/20' => $selectedVehicle === $type,
                            'border-gray-200' => $selectedVehicle !== $type,
                        ])>
                            <input
                                type="radio"
                                name="vehicle_type"
                                value="{{ $type }}"
                                @checked($selectedVehicle === $type)
                                required
                                class="mt-1 h-4 w-4 border-gray-300 text-forest-600 focus:ring-forest-600"
                            >
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-forest-600 shadow-sm ring-1 ring-gray-100">
                                <i class="{{ $icon }} text-xl" aria-hidden="true"></i>
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-gray-900">{{ $type }}</span>
                                <span class="mt-0.5 block text-xs text-gray-500">{{ $description }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('vehicle_type')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="max-w-md">
                    <label class="mb-1 block text-sm font-medium">Plate Number <span class="font-normal text-gray-400">(optional)</span></label>
                    <input type="text" name="plate_number" value="{{ old('plate_number', $data['plate_number'] ?? '') }}" placeholder="e.g. ABC 1234" class="{{ $inputClass }}">
                </div>

                <div class="grid w-full grid-cols-1 gap-3 md:grid-cols-2">
                    <a href="{{ route('register.rider', ['step' => 1]) }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 px-5 py-2.5 text-center text-sm font-medium hover:bg-gray-50">
                        <i class="ri-arrow-left-line text-lg" aria-hidden="true"></i>
                        Back
                    </a>
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-forest-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">
                        Continue
                        <i class="ri-arrow-right-line text-lg" aria-hidden="true"></i>
                    </button>
                </div>
            </form>
        @elseif ($step === 3)
            <form method="POST" action="{{ route('register.rider.step', 3) }}" class="w-full space-y-6">
                @csrf
                <p class="w-full rounded-lg bg-avocado-50/60 px-4 py-3 text-sm text-gray-600">This address helps us verify your application and reach you when needed.</p>
                <x-address-form :regions="$regions" :address="$data" />
                <div class="grid w-full grid-cols-1 gap-3 md:grid-cols-2">
                    <a href="{{ route('register.rider', ['step' => 2]) }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 px-5 py-2.5 text-center text-sm font-medium hover:bg-gray-50">
                        <i class="ri-arrow-left-line text-lg" aria-hidden="true"></i>
                        Back
                    </a>
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-forest-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">
                        Continue
                        <i class="ri-arrow-right-line text-lg" aria-hidden="true"></i>
                    </button>
                </div>
            </form>
        @elseif ($step === 4)
            <form method="POST" action="{{ route('register.rider.step', 4) }}" enctype="multipart/form-data" class="w-full space-y-6">
                @csrf
                <p class="w-full rounded-lg bg-avocado-50/60 px-4 py-3 text-sm text-gray-600">Upload clear copies of your valid ID and driver's license. These are required for rider approval.</p>
                <div class="grid w-full grid-cols-1 gap-x-6 gap-y-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Valid ID</label>
                        <x-file-input name="valid_id" accept=".jpg,.jpeg,.png,.pdf" required hint="JPG, PNG, or PDF · max 5MB" />
                        @error('valid_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Driver's License</label>
                        <x-file-input name="driver_license" accept=".jpg,.jpeg,.png,.pdf" required hint="JPG, PNG, or PDF · max 5MB" />
                        @error('driver_license')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium">Notes <span class="font-normal text-gray-400">(optional)</span></label>
                        <textarea name="notes" rows="3" placeholder="Any additional information for the admin reviewer" class="{{ $inputClass }}">{{ old('notes', $data['notes'] ?? '') }}</textarea>
                    </div>
                </div>
                <div class="grid w-full grid-cols-1 gap-3 md:grid-cols-2">
                    <a href="{{ route('register.rider', ['step' => 3]) }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 px-5 py-2.5 text-center text-sm font-medium hover:bg-gray-50">
                        <i class="ri-arrow-left-line text-lg" aria-hidden="true"></i>
                        Back
                    </a>
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-forest-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">
                        Continue
                        <i class="ri-arrow-right-line text-lg" aria-hidden="true"></i>
                    </button>
                </div>
            </form>
        @elseif ($step === 5)
            @php
                $addressParts = array_filter([
                    $data['street_address'] ?? null,
                    $data['barangay_name'] ?? null,
                    $data['city_name'] ?? null,
                    $data['province_name'] ?? null,
                    $data['region_name'] ?? null,
                    ! empty($data['postal_code']) ? 'ZIP '.$data['postal_code'] : null,
                ]);
            @endphp

            <div class="mb-6 mt-3 rounded-xl border border-avocado-100 bg-avocado-50/50 px-5 py-4 sm:flex sm:items-center sm:justify-between sm:gap-4">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-forest-600 text-white">
                        <i class="ri-checkbox-circle-line text-xl" aria-hidden="true"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-gray-900">You're almost done!</p>
                        <p class="mt-0.5 text-sm text-gray-600">Review your details below, then submit your rider application for admin approval.</p>
                    </div>
                </div>
            </div>

            <div class="grid w-full grid-cols-1 gap-5 md:grid-cols-2">
                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                    <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/80 px-5 py-3">
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-forest-600/10 text-forest-600">
                                <i class="ri-user-3-line text-base" aria-hidden="true"></i>
                            </span>
                            <h3 class="text-sm font-bold text-gray-900">Account</h3>
                        </div>
                        <a href="{{ route('register.rider', ['step' => 1]) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Edit</a>
                    </div>
                    <dl class="space-y-4 p-5 text-sm">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Full name</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ $data['name'] ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Email</dt>
                            <dd class="mt-1 font-medium text-gray-900 break-all">{{ $data['email'] ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Phone</dt>
                            <dd class="mt-1 font-medium text-gray-900">{{ $data['phone'] ?? '—' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                    <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/80 px-5 py-3">
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600/10 text-brand-600">
                                <i class="ri-riding-line text-base" aria-hidden="true"></i>
                            </span>
                            <h3 class="text-sm font-bold text-gray-900">Vehicle</h3>
                        </div>
                        <a href="{{ route('register.rider', ['step' => 2]) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Edit</a>
                    </div>
                    <dl class="space-y-4 p-5 text-sm">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Vehicle type</dt>
                            <dd class="mt-1 font-semibold text-gray-900">{{ $data['vehicle_type'] ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Plate number</dt>
                            <dd class="mt-1 font-medium text-gray-900">{{ $data['plate_number'] ?? 'Not provided' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                    <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/80 px-5 py-3">
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600/10 text-brand-600">
                                <i class="ri-map-pin-2-line text-base" aria-hidden="true"></i>
                            </span>
                            <h3 class="text-sm font-bold text-gray-900">Home address</h3>
                        </div>
                        <a href="{{ route('register.rider', ['step' => 3]) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Edit</a>
                    </div>
                    <div class="p-5 text-sm">
                        <span class="inline-flex rounded-full bg-avocado-50 px-2.5 py-0.5 text-xs font-semibold text-forest-700">
                            {{ $data['label'] ?? 'Home' }}
                        </span>
                        <p class="mt-3 font-medium leading-relaxed text-gray-900">
                            {{ implode(', ', $addressParts) ?: '—' }}
                        </p>
                        @if (! empty($data['landmark']))
                            <p class="mt-3 flex items-start gap-2 text-gray-600">
                                <i class="ri-signpost-line mt-0.5 shrink-0 text-brand-600" aria-hidden="true"></i>
                                <span>{{ $data['landmark'] }}</span>
                            </p>
                        @endif
                        @if (! empty($data['latitude']) && ! empty($data['longitude']))
                            <p class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                                <i class="ri-map-pin-user-line text-brand-600" aria-hidden="true"></i>
                                Location pinned on map
                            </p>
                        @endif
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                    <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/80 px-5 py-3">
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-forest-600/10 text-forest-600">
                                <i class="ri-file-upload-line text-base" aria-hidden="true"></i>
                            </span>
                            <h3 class="text-sm font-bold text-gray-900">Documents</h3>
                        </div>
                        <a href="{{ route('register.rider', ['step' => 4]) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Edit</a>
                    </div>
                    <dl class="space-y-4 p-5 text-sm">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Valid ID</dt>
                            <dd class="mt-1">
                                @if (! empty($data['valid_id_path']))
                                    <span class="inline-flex items-center gap-1 text-sm font-medium text-forest-700">
                                        <i class="ri-check-line" aria-hidden="true"></i> Uploaded
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">—</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Driver's license</dt>
                            <dd class="mt-1">
                                @if (! empty($data['driver_license_path']))
                                    <span class="inline-flex items-center gap-1 text-sm font-medium text-forest-700">
                                        <i class="ri-check-line" aria-hidden="true"></i> Uploaded
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">—</span>
                                @endif
                            </dd>
                        </div>
                        @if (! empty($data['notes']))
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Notes</dt>
                                <dd class="mt-1 rounded-lg bg-avocado-50/60 px-3 py-2 text-gray-700">{{ $data['notes'] }}</dd>
                            </div>
                        @endif
                    </dl>
                </section>
            </div>

            <form method="POST" action="{{ route('register.rider.submit') }}" class="mt-8 overflow-hidden rounded-xl border border-forest-600/20 bg-gradient-to-r from-forest-600/5 to-avocado-50/40 p-5 sm:p-6">
                @csrf
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold text-gray-900">Ready to submit your application?</p>
                        <p class="mt-0.5 text-sm text-gray-600">Your rider account will be reviewed by our admin team before you can accept deliveries.</p>
                    </div>
                    <div class="flex shrink-0 flex-col gap-3 sm:flex-row">
                        <a href="{{ route('register.rider', ['step' => 4]) }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-center text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="ri-arrow-left-line text-lg" aria-hidden="true"></i>
                            Back
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-forest-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-forest-700">
                            <i class="ri-riding-line text-lg" aria-hidden="true"></i>
                            Submit registration
                        </button>
                    </div>
                </div>
            </form>
        @endif
        </div>
    </div>

    <p class="mt-8 text-center text-sm text-gray-500">
        <a href="{{ route('join') }}" class="text-brand-600 hover:underline">Other registration options</a>
        ·
        Already have an account? <a href="{{ route('login') }}" class="text-brand-600 hover:underline">Sign in</a>
    </p>
</div>
@endsection
