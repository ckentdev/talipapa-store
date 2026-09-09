@props(['order'])

@php
    use App\Models\StoreProfile;
    use App\Models\User;

    $storeReview = $order->reviews->first(
        fn ($review) => $review->reviewable_type === StoreProfile::class
            && (int) $review->reviewable_id === (int) $order->store_profile_id
    );

    $riderReview = $order->rider_id
        ? $order->reviews->first(
            fn ($review) => $review->reviewable_type === User::class
                && (int) $review->reviewable_id === (int) $order->rider_id
        )
        : null;

    $reviewCount = ($storeReview ? 1 : 0) + ($riderReview ? 1 : 0);
    $totalReviews = $order->rider_id ? 2 : 1;
@endphp

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm']) }}>
    <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 via-white to-brand-50/40 px-6 py-5 sm:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-4">
                <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                    <i class="ri-star-smile-line text-2xl" aria-hidden="true"></i>
                </span>
                <div>
                    <p class="text-base font-bold uppercase tracking-widest text-brand-600">Feedback</p>
                    <h2 class="mt-0.5 text-xl font-bold text-gray-900 sm:text-2xl">Rate Your Experience</h2>
                    <p class="mt-1 text-base text-gray-600">
                        Tell us about your order from {{ $order->store?->store_name }}.
                    </p>
                </div>
            </div>

            <div class="inline-flex w-fit items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-base text-gray-700 shadow-sm">
                <i class="ri-chat-smile-2-line text-brand-600" aria-hidden="true"></i>
                {{ $reviewCount }} of {{ $totalReviews }} reviewed
            </div>
        </div>

        <div class="mt-5 h-2 overflow-hidden rounded-full bg-gray-100">
            <div
                class="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-600 transition-all duration-500"
                style="width: {{ $totalReviews > 0 ? (int) round(($reviewCount / $totalReviews) * 100) : 0 }}%"
            ></div>
        </div>
    </div>

    <div @class([
        'grid gap-6 p-6 sm:p-8',
        'lg:grid-cols-2' => $order->rider_id,
    ])>
        <x-customer.orders.review-card
            :order="$order"
            type="store"
            :title="$order->store?->store_name ?? 'Store'"
            subtitle="How was the product quality and store service?"
            icon="ri-store-2-line"
            :existing-review="$storeReview"
        />

        @if ($order->rider_id)
            <x-customer.orders.review-card
                :order="$order"
                type="rider"
                :title="$order->rider->name"
                subtitle="How was the delivery speed and rider service?"
                icon="ri-e-bike-line"
                :existing-review="$riderReview"
            />
        @endif
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-star-rating]').forEach((group) => {
                    const inputs = [...group.querySelectorAll('[data-star-input]')];
                    const labels = [...group.querySelectorAll('[data-star-label]')];
                    const caption = group.querySelector('[data-star-caption]');

                    const paint = (value) => {
                        labels.forEach((label) => {
                            const starValue = Number(label.dataset.starLabel);
                            const active = value > 0 && starValue <= value;

                            label.classList.toggle('text-yellow-400', active);
                            label.classList.toggle('text-gray-300', ! active);
                        });

                        if (caption) {
                            caption.textContent = value > 0
                                ? `${value} out of 5 star${value === 1 ? '' : 's'}`
                                : 'Select a rating';
                        }
                    };

                    inputs.forEach((input) => {
                        input.addEventListener('change', () => paint(Number(input.value)));
                    });

                    labels.forEach((label) => {
                        label.addEventListener('mouseenter', () => paint(Number(label.dataset.starLabel)));
                    });

                    group.addEventListener('mouseleave', () => {
                        const checked = group.querySelector('[data-star-input]:checked');
                        paint(checked ? Number(checked.value) : 0);
                    });

                    const checked = group.querySelector('[data-star-input]:checked');
                    paint(checked ? Number(checked.value) : 0);
                });
            });
        </script>
    @endpush
@endonce
