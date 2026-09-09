@props([
    'count',
    'compact' => false,
])

@if ($count > 0)
    @if ($compact)
        <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-0.5']) }}>
            <span class="relative flex h-2 w-2" aria-hidden="true">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500 ring-1 ring-white"></span>
            </span>
            <span class="inline-flex h-3.5 min-w-[0.875rem] items-center justify-center rounded-full bg-green-500 px-0.5 text-[8px] font-bold leading-none text-white ring-1 ring-white">
                {{ $count > 99 ? '99+' : $count }}
            </span>
            <span class="sr-only">{{ $count }} available</span>
        </span>
    @else
        <span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center gap-1.5 rounded-full bg-green-50 px-2 py-0.5 ring-1 ring-green-200/80']) }}>
            <span class="relative flex h-2 w-2" aria-hidden="true">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500"></span>
            </span>
            <span class="text-[10px] font-bold tabular-nums text-green-700">
                {{ $count > 99 ? '99+' : $count }}
            </span>
            <span class="sr-only">{{ $count }} available</span>
        </span>
    @endif
@endif
