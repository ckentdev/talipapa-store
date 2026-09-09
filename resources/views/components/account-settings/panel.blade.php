@props([
    'icon',
    'title',
    'description',
])

<div {{ $attributes->merge(['class' => 'space-y-5']) }}>
    <div class="flex flex-col items-start gap-4 rounded-lg border border-gray-200 bg-gradient-to-r from-avocado-50/60 to-white p-4 sm:flex-row sm:items-center">
        <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-forest-600/10 text-forest-600">
            <i class="{{ $icon }} text-2xl" aria-hidden="true"></i>
        </span>
        <div class="min-w-0">
            <h3 class="text-base font-bold text-gray-900">{{ $title }}</h3>
            <p class="mt-1 text-base text-gray-500">{{ $description }}</p>
        </div>
    </div>

    {{ $slot }}
</div>
