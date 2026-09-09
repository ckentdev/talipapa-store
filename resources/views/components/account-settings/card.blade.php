@props([
    'title' => null,
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-lg border border-gray-200 bg-white']) }}>
    @if ($title)
        <div class="border-b border-gray-100 px-4 py-3 sm:px-5">
            <h4 class="text-base font-semibold text-gray-900">{{ $title }}</h4>
            @if ($description)
                <p class="mt-0.5 text-base text-gray-500">{{ $description }}</p>
            @endif
        </div>
    @endif

    <div class="p-4 sm:p-5">
        {{ $slot }}
    </div>
</div>
