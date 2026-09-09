@props([
    'name',
    'nameId',
    'ratingName',
    'rating' => 5,
    'required' => true,
])

<div {{ $attributes->merge(['class' => 'space-y-2']) }} data-star-rating>
    <p class="text-base font-medium text-gray-700">Your rating</p>
    <div class="flex items-center gap-1" role="radiogroup" aria-label="Rating">
        @for ($i = 1; $i <= 5; $i++)
            <input
                type="radio"
                id="{{ $nameId }}-star-{{ $i }}"
                name="{{ $ratingName }}"
                value="{{ $i }}"
                @checked((int) old('rating', $rating) === $i)
                @if ($required) required @endif
                class="peer sr-only"
                data-star-input
            >
            <label
                for="{{ $nameId }}-star-{{ $i }}"
                class="cursor-pointer rounded-md p-0.5 text-gray-300 transition hover:scale-110 hover:text-yellow-300"
                data-star-label="{{ $i }}"
                aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}"
            >
                <i class="ri-star-fill text-3xl" aria-hidden="true"></i>
            </label>
        @endfor
    </div>
    <p class="text-sm text-gray-500" data-star-caption>
        {{ (int) old('rating', $rating) }} out of 5 stars
    </p>
</div>
