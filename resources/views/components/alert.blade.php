@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => true,
])

@php
    $styles = match ($type) {
        'success' => [
            'border' => 'border-forest-200',
            'bg' => 'bg-gradient-to-r from-forest-50/90 to-avocado-50/50',
            'iconWrap' => 'bg-forest-600 text-white',
            'icon' => 'ri-checkbox-circle-line',
            'title' => 'text-forest-800',
            'text' => 'text-forest-700',
            'close' => 'text-forest-600 hover:bg-forest-100',
        ],
        'error' => [
            'border' => 'border-red-200',
            'bg' => 'bg-gradient-to-r from-red-50/90 to-white',
            'iconWrap' => 'bg-red-500 text-white',
            'icon' => 'ri-error-warning-line',
            'title' => 'text-red-800',
            'text' => 'text-red-700',
            'close' => 'text-red-600 hover:bg-red-100',
        ],
        'warning' => [
            'border' => 'border-orange-200',
            'bg' => 'bg-gradient-to-r from-orange-50/90 to-avocado-50/40',
            'iconWrap' => 'bg-orange-500 text-white',
            'icon' => 'ri-alert-line',
            'title' => 'text-orange-800',
            'text' => 'text-orange-700',
            'close' => 'text-orange-600 hover:bg-orange-100',
        ],
        default => [
            'border' => 'border-brand-200',
            'bg' => 'bg-gradient-to-r from-avocado-50/90 to-white',
            'iconWrap' => 'bg-brand-600 text-white',
            'icon' => 'ri-information-line',
            'title' => 'text-gray-900',
            'text' => 'text-gray-700',
            'close' => 'text-gray-500 hover:bg-gray-100',
        ],
    };
@endphp

<div
    {{ $attributes->merge(['class' => "flex gap-3 rounded-xl border p-4 shadow-sm {$styles['border']} {$styles['bg']}"]) }}
    role="alert"
    data-alert
>
    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $styles['iconWrap'] }}">
        <i class="{{ $styles['icon'] }} text-lg" aria-hidden="true"></i>
    </span>
    <div class="min-w-0 flex-1 pt-0.5">
        @if ($title)
            <p class="text-sm font-semibold {{ $styles['title'] }}">{{ $title }}</p>
        @endif
        <div @class(['text-sm leading-relaxed', $styles['text'], 'mt-0.5' => $title])>
            {{ $slot }}
        </div>
    </div>
    @if ($dismissible)
        <button
            type="button"
            data-alert-dismiss
            class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition {{ $styles['close'] }}"
            aria-label="Dismiss alert"
        >
            <i class="ri-close-line text-lg" aria-hidden="true"></i>
        </button>
    @endif
</div>
