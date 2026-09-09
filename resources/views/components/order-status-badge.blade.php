@props([
    'status',
])

@php
    use App\Enums\OrderStatus;

    $orderStatus = $status instanceof OrderStatus
        ? $status
        : OrderStatus::tryFrom((string) $status);

    $color = $orderStatus?->color() ?? 'gray';

    $classes = match ($color) {
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'blue' => 'bg-blue-100 text-blue-800',
        'red' => 'bg-red-100 text-red-800',
        'indigo' => 'bg-indigo-100 text-indigo-800',
        'purple' => 'bg-purple-100 text-purple-800',
        'cyan' => 'bg-cyan-100 text-cyan-800',
        'orange' => 'bg-orange-100 text-orange-800',
        'green' => 'bg-green-100 text-green-800',
        default => 'bg-gray-100 text-gray-800',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {$classes}"]) }}>
    {{ $orderStatus?->label() ?? ucfirst(str_replace('_', ' ', (string) $status)) }}
</span>
