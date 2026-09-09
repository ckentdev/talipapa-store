<div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-200 bg-white px-6 py-12 text-center">
    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-600">
        @if (! empty($icon))
            {!! $icon !!}
        @else
            <i class="ri-inbox-line text-2xl" aria-hidden="true"></i>
        @endif
    </div>

    <h3 class="text-base font-semibold text-gray-900">{{ $title ?? 'Nothing here yet' }}</h3>

    @if (! empty($message))
        <p class="mt-2 max-w-sm text-sm text-gray-500">{{ $message }}</p>
    @endif
</div>
