@php
    $faqs = config('landing.faqs');
@endphp

<section id="faq" class="bg-white py-10 sm:py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <p class="mb-2 text-sm font-bold uppercase tracking-wider text-brand-600 sm:text-base">Help center</p>
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl lg:text-5xl">Frequently Asked Questions</h2>
            <p class="mx-auto mt-4 max-w-2xl text-base text-gray-500 sm:text-lg">
                Quick answers about shopping, delivery, and getting started on Talipapa.
            </p>
        </div>

        <div id="landing-faq-accordion" data-accordion="collapse">
            @foreach ($faqs as $index => $faq)
                @php
                    $headingId = 'landing-faq-heading-'.$index;
                    $bodyId = 'landing-faq-body-'.$index;
                    $isFirst = $index === 0;
                @endphp

                <div @class(['border-b border-gray-200', 'border-t' => $isFirst])>
                    <h3 id="{{ $headingId }}">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-4 py-5 text-left text-base font-semibold text-gray-900 transition hover:text-brand-600 sm:py-6 sm:text-lg lg:text-xl"
                            data-accordion-target="#{{ $bodyId }}"
                            aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                            aria-controls="{{ $bodyId }}"
                        >
                            <span>{{ $faq['question'] }}</span>
                            <i
                                data-accordion-icon
                                class="ri-arrow-down-s-line shrink-0 text-2xl text-gray-400 transition-transform sm:text-3xl"
                                aria-hidden="true"
                            ></i>
                        </button>
                    </h3>
                    <div
                        id="{{ $bodyId }}"
                        @class(['hidden' => ! $isFirst])
                        aria-labelledby="{{ $headingId }}"
                    >
                        <p class="pb-5 text-base leading-relaxed text-gray-500 sm:pb-6 sm:text-lg lg:text-xl">
                            {{ $faq['answer'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
