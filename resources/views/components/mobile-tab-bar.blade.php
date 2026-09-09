@props([
    'items' => [],
])

@php
    $count = count($items);
    $scrollable = $count > 5;
@endphp

<div {{ $attributes->merge(['class' => 'fixed inset-x-0 bottom-0 z-40 lg:hidden']) }}>
    <div class="overflow-hidden rounded-t-2xl border-t border-avocado-100/90 bg-white/95 shadow-[0_-12px_40px_-12px_rgba(27,67,50,0.15)] backdrop-blur-lg pb-[env(safe-area-inset-bottom,0px)]">
        <div
            @class([
                'mx-auto max-w-lg px-1.5 pt-1',
                'grid' => ! $scrollable,
                'flex snap-x snap-mandatory gap-0.5 overflow-x-auto px-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden' => $scrollable,
            ])
            @unless ($scrollable)
                style="grid-template-columns: repeat({{ max($count, 1) }}, minmax(0, 1fr));"
            @endunless
            role="tablist"
            aria-label="Mobile navigation"
        >
            @foreach ($items as $item)
                @php
                    $active = request()->routeIs($item['patterns'] ?? []);
                    $label = $item['mobileLabel'] ?? $item['label'];
                    $href = str_starts_with($item['route'], 'http')
                        ? $item['route']
                        : route($item['route']);
                @endphp

                <a
                    href="{{ $href }}"
                    role="tab"
                    aria-selected="{{ $active ? 'true' : 'false' }}"
                    @class([
                        'group relative flex min-w-0 flex-col items-center gap-1 rounded-2xl px-1 py-2 transition-colors',
                        'snap-center shrink-0 w-[5rem]' => $scrollable,
                        'active:bg-forest-600/5' => ! $active,
                    ])
                >
                    @if ($active)
                        <span class="absolute inset-x-1 top-1.5 h-9 rounded-2xl bg-forest-600/10" aria-hidden="true"></span>
                    @endif

                    <span @class([
                        'relative flex h-9 w-9 items-center justify-center rounded-2xl transition-all duration-200',
                        'bg-forest-600 text-white shadow-md shadow-forest-600/30' => $active,
                        'text-gray-400 group-hover:text-forest-600' => ! $active,
                    ])>
                        @include('layouts.partials._nav_icon', [
                            'icon' => $item['icon'],
                            'active' => $active,
                            'mobile' => true,
                            'inverted' => $active,
                        ])

                        @if (! empty($item['badge']) && ($item['badgeType'] ?? null) === 'online')
                            <span class="absolute -right-1 -top-1">
                                <x-nav-online-badge :count="$item['badge']" compact />
                            </span>
                        @elseif (! empty($item['badge']) && ($item['badgeType'] ?? null) === 'pending')
                            <span class="absolute -right-1 -top-1">
                                <x-nav-pending-badge :count="$item['badge']" compact />
                            </span>
                        @elseif (! empty($item['badge']))
                            <span class="absolute -right-1 -top-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-accent-500 px-0.5 text-[8px] font-bold leading-none text-gray-900 ring-2 ring-white">
                                {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                                <span class="sr-only">{{ $item['badge'] }} items</span>
                            </span>
                        @endif
                    </span>

                    <span @class([
                        'relative max-w-full truncate text-xs font-semibold leading-tight',
                        'text-forest-600' => $active,
                        'text-gray-500' => ! $active,
                    ])>
                        {{ $label }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</div>
