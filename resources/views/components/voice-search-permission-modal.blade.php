<div
    id="voice-search-permission-modal"
    class="fixed inset-0 z-[100] hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="voice-search-permission-title"
    aria-describedby="voice-search-permission-message"
>
    <div data-voice-perm-backdrop class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl">
            <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-6 py-5">
                <div class="flex items-start gap-4">
                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-600">
                        <i class="ri-mic-line text-xl" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h3 id="voice-search-permission-title" class="text-base font-bold text-gray-900">Enable voice search</h3>
                        <p id="voice-search-permission-message" class="mt-1 text-base text-gray-600">
                            Allow microphone access to search products by voice.
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-4 px-6 py-5">
                <div class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 bg-gray-50/60 px-4 py-3">
                    <div class="min-w-0">
                        <p class="text-base font-semibold text-gray-900">Sound</p>
                        <p class="text-sm text-gray-500">Plays feedback when listening starts and ends.</p>
                        <p class="mt-1 text-xs font-medium" data-voice-perm-sound-status>Not enabled</p>
                    </div>
                    <button
                        type="button"
                        data-voice-perm-enable-sound
                        class="shrink-0 rounded-lg border border-brand-600 px-3 py-2 text-sm font-semibold text-brand-600 transition hover:bg-brand-50"
                    >
                        Allow sound
                    </button>
                </div>

                <div class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 bg-gray-50/60 px-4 py-3">
                    <div class="min-w-0">
                        <p class="text-base font-semibold text-gray-900">Microphone</p>
                        <p class="text-sm text-gray-500">Needed to hear what you say in the search bar.</p>
                        <p class="mt-1 text-xs font-medium" data-voice-perm-mic-status>Not enabled</p>
                    </div>
                    <button
                        type="button"
                        data-voice-perm-enable-mic
                        class="shrink-0 rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    >
                        Allow microphone
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4">
                <button
                    type="button"
                    data-voice-perm-cancel
                    class="rounded-lg border border-gray-200 px-4 py-2.5 text-base font-medium text-gray-700 transition hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    data-voice-perm-start
                    disabled
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-base font-semibold text-white transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <i class="ri-mic-line" aria-hidden="true"></i>
                    Start listening
                </button>
            </div>
        </div>
    </div>
</div>
