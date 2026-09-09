@props(['order', 'timeline'])

@php
    $statusIcons = [
        \App\Enums\OrderStatus::Pending->value => 'ri-time-line',
        \App\Enums\OrderStatus::Accepted->value => 'ri-checkbox-circle-line',
        \App\Enums\OrderStatus::Preparing->value => 'ri-restaurant-line',
        \App\Enums\OrderStatus::ReadyForPickup->value => 'ri-shopping-bag-3-line',
        \App\Enums\OrderStatus::RiderAssigned->value => 'ri-e-bike-line',
        \App\Enums\OrderStatus::PickedUp->value => 'ri-truck-line',
        \App\Enums\OrderStatus::Delivered->value => 'ri-home-smile-line',
    ];

    $timelineValues = $timeline->values()->all();
    $currentIndex = array_search($order->status, $timelineValues, true);
    $isTerminal = in_array($order->status, [\App\Enums\OrderStatus::Rejected, \App\Enums\OrderStatus::Cancelled], true);
@endphp

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm']) }}>
    <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-6 py-5 sm:px-8">
        <div class="flex items-start gap-4">
            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                <i class="ri-route-line text-xl" aria-hidden="true"></i>
            </span>
            <div>
                <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Order Timeline</h2>
                <p class="mt-0.5 text-base text-gray-600">Follow your order from placement to delivery.</p>
            </div>
        </div>
    </div>

    <div class="p-6 sm:p-8">
        @if ($isTerminal)
            <div @class([
                'rounded-xl border px-4 py-3 text-base font-medium',
                'border-red-200 bg-red-50 text-red-700' => $order->status === \App\Enums\OrderStatus::Rejected,
                'border-gray-200 bg-gray-50 text-gray-700' => $order->status === \App\Enums\OrderStatus::Cancelled,
            ])>
                @if ($order->status === \App\Enums\OrderStatus::Rejected)
                    <i class="ri-close-circle-line me-1.5" aria-hidden="true"></i>
                    Rejected{{ $order->rejection_reason ? ': '.$order->rejection_reason : '' }}
                @else
                    <i class="ri-forbid-line me-1.5" aria-hidden="true"></i>
                    Order cancelled
                @endif
            </div>
        @else
            <ol class="relative space-y-0">
                @foreach ($timeline as $status)
                    @php
                        $index = array_search($status, $timelineValues, true);
                        $isComplete = $currentIndex !== false && $index <= $currentIndex;
                        $isCurrent = $order->status === $status;
                        $icon = $statusIcons[$status->value] ?? 'ri-circle-line';
                    @endphp
                    <li class="relative flex gap-4 pb-8 last:pb-0">
                        @if (! $loop->last)
                            <span
                                @class([
                                    'absolute start-5 top-10 -ms-px h-[calc(100%-2rem)] w-0.5',
                                    'bg-brand-500' => $isComplete && ! $isCurrent,
                                    'bg-brand-200' => $isCurrent,
                                    'bg-gray-200' => ! $isComplete,
                                ])
                                aria-hidden="true"
                            ></span>
                        @endif

                        <span
                            @class([
                                'relative z-10 flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 transition',
                                'border-brand-600 bg-brand-600 text-white shadow-md ring-4 ring-brand-600/15' => $isCurrent,
                                'border-brand-200 bg-brand-50 text-brand-700' => $isComplete && ! $isCurrent,
                                'border-gray-200 bg-white text-gray-400' => ! $isComplete,
                            ])
                        >
                            @if ($isComplete && ! $isCurrent)
                                <i class="ri-check-line text-base" aria-hidden="true"></i>
                            @else
                                <i class="{{ $icon }} text-base" aria-hidden="true"></i>
                            @endif
                        </span>

                        <div class="min-w-0 flex-1 pt-1.5">
                            <p @class([
                                'text-base font-semibold',
                                'text-brand-600' => $isCurrent,
                                'text-gray-900' => $isComplete && ! $isCurrent,
                                'text-gray-400' => ! $isComplete,
                            ])>
                                {{ $status->label() }}
                            </p>
                            @if ($isCurrent)
                                <p class="mt-0.5 text-base text-gray-500">Current status</p>
                            @elseif ($isComplete)
                                <p class="mt-0.5 text-base text-gray-500">Completed</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</div>
