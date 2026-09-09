@props([
    'step',
])

@php
    $steps = [
        1 => 'Cart',
        2 => 'Info',
        3 => 'Address',
        4 => 'Payment',
        5 => 'Confirm',
    ];

    $icons = [
        1 => 'ri-shopping-cart-2-line',
        2 => 'ri-user-3-line',
        3 => 'ri-map-pin-2-line',
        4 => 'ri-bank-card-line',
        5 => 'ri-checkbox-circle-line',
    ];

    $totalSteps = count($steps);
    $progressPercent = $totalSteps > 1
        ? (int) round((($step - 1) / ($totalSteps - 1)) * 100)
        : 100;
@endphp

<div {{ $attributes->merge(['class' => 'mb-6 sm:mb-8']) }}>
    <div class="overflow-hidden rounded-2xl border border-avocado-100/90 bg-white/90 p-4 shadow-sm backdrop-blur-sm sm:p-6">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-base font-bold uppercase tracking-widest text-brand-600">Checkout</p>
                <p class="mt-0.5 text-base font-semibold text-gray-900">
                    Step {{ $step }} of {{ $totalSteps }}
                    <span class="font-normal text-gray-400">·</span>
                    <span class="font-medium text-gray-600">{{ $steps[$step] ?? '' }}</span>
                </p>
            </div>
            <span class="inline-flex w-fit shrink-0 rounded-full bg-avocado-50 px-3 py-1 text-base font-semibold text-brand-600">
                {{ $progressPercent }}% complete
            </span>
        </div>

        <div
            class="h-2 overflow-hidden rounded-full bg-gray-100"
            role="progressbar"
            aria-valuenow="{{ $progressPercent }}"
            aria-valuemin="0"
            aria-valuemax="100"
            aria-label="Checkout progress"
        >
            <div
                class="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-600 transition-all duration-500 ease-out"
                style="width: {{ max($progressPercent, $step === 1 ? 8 : 0) }}%"
            ></div>
        </div>

        <ol class="mt-6 flex w-full items-start">
            @foreach ($steps as $num => $label)
                @php
                    $isActive = $num === $step;
                    $isComplete = $num < $step;
                    $icon = $icons[$num] ?? null;
                    $lineLeftActive = $num <= $step;
                    $lineRightActive = $num < $step;
                @endphp
                <li class="flex min-w-0 flex-1 flex-col items-center">
                    <div class="flex w-full items-center">
                        <span
                            @class([
                                'h-0.5 flex-1',
                                $loop->first ? 'bg-transparent' : ($lineLeftActive ? 'bg-brand-500' : 'bg-gray-200'),
                            ])
                            aria-hidden="true"
                        ></span>

                        <span
                            @class([
                                'relative z-10 mx-2 flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 text-sm font-bold transition-all duration-300 sm:mx-3 sm:h-11 sm:w-11 sm:text-base',
                                'scale-105 shadow-md ring-4 ring-brand-600/15 border-brand-600 bg-brand-600 text-white' => $isActive,
                                'border-brand-200 bg-brand-50 text-brand-700' => $isComplete,
                                'border-gray-200 bg-white text-gray-400' => ! $isActive && ! $isComplete,
                            ])
                            @if ($isActive) aria-current="step" @endif
                        >
                            @if ($isComplete)
                                <i class="ri-check-line text-base sm:text-lg" aria-hidden="true"></i>
                            @elseif ($icon)
                                <i class="{{ $icon }} text-base sm:text-lg" aria-hidden="true"></i>
                            @else
                                {{ $num }}
                            @endif
                        </span>

                        <span
                            @class([
                                'h-0.5 flex-1',
                                $loop->last ? 'bg-transparent' : ($lineRightActive ? 'bg-brand-500' : 'bg-gray-200'),
                            ])
                            aria-hidden="true"
                        ></span>
                    </div>

                    <span
                        @class([
                            'mt-2 w-full px-1 text-center text-base font-semibold leading-tight',
                            'text-gray-900' => $isActive,
                            'text-brand-600' => $isComplete,
                            'text-gray-400' => ! $isActive && ! $isComplete,
                        ])
                    >
                        {{ $label }}
                    </span>
                </li>
            @endforeach
        </ol>
    </div>
</div>
