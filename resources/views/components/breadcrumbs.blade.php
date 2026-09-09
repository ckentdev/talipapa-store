@props([
    'items' => [],
])

@php
    $items = is_array($items) ? $items : [];
@endphp

@if (count($items) > 0)
    <nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'min-w-0']) }}>
        <ol class="flex min-w-0 flex-wrap items-center gap-x-1.5 gap-y-1 text-sm">
            @foreach ($items as $index => $item)
                @if ($index > 0)
                    <li class="shrink-0 text-gray-300" aria-hidden="true">
                        <i class="ri-arrow-right-s-line text-base"></i>
                    </li>
                @endif

                <li @class(['min-w-0', 'max-w-[12rem] truncate sm:max-w-none' => $index === count($items) - 1])>
                    @if (! empty($item['url']) && $index < count($items) - 1)
                        <a
                            href="{{ $item['url'] }}"
                            class="font-medium text-gray-500 transition hover:text-brand-600"
                        >
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="font-semibold text-gray-900">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
