@props([
    'order',
    'type',
    'title',
    'subtitle',
    'icon',
    'existingReview' => null,
])

@php
    $inputClass = 'block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-base placeholder:text-gray-400 focus:border-brand-600 focus:ring-brand-600';
    $nameId = $type.'-review-'.$order->id;
    $defaultRating = (int) old('rating', $existingReview?->rating ?? 5);
@endphp

<article @class([
    'overflow-hidden rounded-2xl border bg-white shadow-sm transition',
    'border-brand-200 ring-1 ring-brand-100' => $existingReview,
    'border-gray-200' => ! $existingReview,
])>
    <div @class([
        'border-b px-5 py-4 sm:px-6',
        'border-brand-100 bg-gradient-to-r from-brand-50/70 to-white' => $existingReview,
        'border-gray-100 bg-gradient-to-r from-avocado-50/50 to-white' => ! $existingReview,
    ])>
        <div class="flex items-start gap-4">
            <span @class([
                'inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl',
                'bg-brand-600/10 text-brand-600' => $existingReview,
                'bg-gray-100 text-gray-600' => ! $existingReview,
            ])>
                <i class="{{ $icon }} text-xl" aria-hidden="true"></i>
            </span>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-lg font-bold text-gray-900">{{ $title }}</h3>
                    @if ($existingReview)
                        <span class="inline-flex items-center gap-1 rounded-full bg-brand-100 px-2.5 py-0.5 text-sm font-semibold text-brand-700">
                            <i class="ri-check-line" aria-hidden="true"></i>
                            Reviewed
                        </span>
                    @endif
                </div>
                <p class="mt-0.5 text-base text-gray-600">{{ $subtitle }}</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('customer.reviews.store', $order) }}" class="space-y-5 p-5 sm:p-6">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <input type="hidden" name="type" value="{{ $type }}">

        <x-customer.orders.star-rating
            :name-id="$nameId"
            rating-name="rating"
            :rating="$defaultRating"
        />

        <div>
            <label for="{{ $nameId }}-comment" class="mb-1.5 block text-base font-medium text-gray-700">
                Share your experience
                <span class="font-normal text-gray-500">(optional)</span>
            </label>
            <textarea
                id="{{ $nameId }}-comment"
                name="comment"
                rows="4"
                placeholder="What went well? Anything we could improve?"
                class="{{ $inputClass }}"
            >{{ old('comment', $existingReview?->comment) }}</textarea>
        </div>

        <button
            type="submit"
            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-brand-700"
        >
            <i class="ri-star-smile-line text-lg" aria-hidden="true"></i>
            {{ $existingReview ? 'Update review' : 'Submit review' }}
        </button>
    </form>
</article>
