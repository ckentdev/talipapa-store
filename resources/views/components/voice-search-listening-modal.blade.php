<div
    id="voice-search-listening-modal"
    class="fixed inset-0 z-[100] hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="voice-search-listening-title"
    aria-describedby="voice-search-listening-message"
>
    <div data-voice-listening-backdrop class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl">
            <button
                type="button"
                data-voice-listening-close
                class="absolute right-3 top-3 z-10 inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                aria-label="Close"
            >
                <i class="ri-close-line text-xl" aria-hidden="true"></i>
            </button>

            <div class="border-b border-gray-100 bg-gradient-to-r from-brand-50/80 via-white to-avocado-50/60 px-6 pb-8 pt-10 text-center">
                <span
                    data-voice-listening-mic
                    class="relative mx-auto inline-flex h-20 w-20 items-center justify-center rounded-full bg-brand-600/10 text-brand-600 transition-transform duration-100"
                >
                    <span
                        data-voice-listening-ring
                        class="absolute inset-0 rounded-full bg-brand-400/20 transition-transform duration-100"
                        aria-hidden="true"
                    ></span>
                    <span
                        data-voice-listening-pulse
                        class="absolute inset-2 rounded-full bg-brand-400/10"
                        aria-hidden="true"
                    ></span>
                    <i class="ri-mic-fill relative text-4xl" aria-hidden="true"></i>
                </span>

                <div
                    data-voice-listening-visualizer
                    class="mt-5 flex h-12 items-end justify-center gap-1.5"
                    aria-hidden="true"
                >
                    @foreach (range(1, 7) as $bar)
                        <span
                            data-voice-level-bar
                            class="w-1.5 rounded-full bg-brand-500/70 transition-[height,opacity] duration-75 ease-out"
                            style="height: 0.75rem; opacity: 0.35;"
                        ></span>
                    @endforeach
                </div>

                <h3 id="voice-search-listening-title" class="mt-5 text-xl font-bold text-gray-900">I'm listening</h3>
                <p id="voice-search-listening-message" class="mt-2 text-base text-gray-600">
                    Tell me what you're looking for.
                </p>
                <p
                    class="mt-3 hidden text-sm font-medium text-brand-600"
                    data-voice-listening-status
                    role="status"
                    aria-live="polite"
                ></p>
            </div>

            <div
                data-voice-listening-actions
                class="hidden items-center justify-center gap-3 border-t border-gray-100 px-6 py-4"
            >
                <button
                    type="button"
                    data-voice-listening-cancel
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-base font-semibold text-white transition hover:bg-brand-700"
                >
                    <i class="ri-stop-circle-line text-lg" aria-hidden="true"></i>
                    Stop speaking
                </button>
            </div>
        </div>
    </div>
</div>
