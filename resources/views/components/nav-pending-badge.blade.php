@props([
    'count',
    'compact' => false,
])

@if ($count > 0)
    @php
        $label = 'x'.($count > 99 ? '99+' : $count);
    @endphp

    @if ($compact)
        <span {{ $attributes->merge(['class' => 'inline-flex h-4 min-w-[1.125rem] items-center justify-center rounded-full bg-red-500 px-1 text-[8px] font-bold leading-none text-white ring-1 ring-white']) }}>
            {{ $label }}
            <span class="sr-only">{{ $count }} pending</span>
        </span>
    @else
        <span {{ $attributes->merge(['class' => 'inline-flex min-w-[1.75rem] shrink-0 items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-bold tabular-nums text-white']) }}>
            {{ $label }}
            <span class="sr-only">{{ $count }} pending</span>
        </span>
    @endif
@endif
