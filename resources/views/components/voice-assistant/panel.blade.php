@if (config('voice-assistant.enabled') && filled(config('voice-assistant.openai_api_key')))
    <div
        data-voice-assistant
        class="fixed inset-x-0 bottom-0 z-50 flex max-h-[85vh] translate-y-full flex-col rounded-t-3xl border border-gray-200 bg-white shadow-2xl transition-transform duration-300 pointer-events-none"
        aria-hidden="true"
        role="dialog"
        aria-labelledby="voice-assistant-title"
    >
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <div>
                <h2 id="voice-assistant-title" class="text-lg font-bold text-gray-900">Voice assistant</h2>
                <p data-voice-assistant-status class="text-sm text-gray-500">Tap the microphone to start</p>
            </div>
            <button
                type="button"
                data-voice-assistant-close
                class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700"
                aria-label="Close voice assistant"
            >
                <i class="ri-close-line text-xl" aria-hidden="true"></i>
            </button>
        </div>

        <div
            data-voice-assistant-messages
            class="flex-1 space-y-3 overflow-y-auto px-4 py-4"
        ></div>

        <div data-voice-assistant-products class="max-h-48 space-y-2 overflow-y-auto border-t border-gray-100 px-4 py-3"></div>

        <div class="safe-area-pb flex items-center gap-3 border-t border-gray-100 px-4 py-4">
            <button
                type="button"
                data-voice-assistant-mic
                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white hover:bg-brand-700"
                aria-label="Speak"
            >
                <i class="ri-mic-fill text-xl" aria-hidden="true"></i>
            </button>
            <button
                type="button"
                data-voice-assistant-stop
                class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Stop recording
            </button>
            <p class="text-xs text-gray-500">English, Tagalog, or Bisaya</p>
        </div>
    </div>
@endif
