@if (config('voice-assistant.search_enabled') && filled(config('voice-assistant.openai_api_key')))
    <button
        type="button"
        data-voice-search-fab
        class="fixed right-4 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-brand-600 text-white shadow-lg ring-4 ring-white/80 transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 bottom-[calc(5.5rem+env(safe-area-inset-bottom,0px))] lg:bottom-8"
        aria-label="Search products by voice"
        aria-pressed="false"
        title="Speak to search products"
    >
        <i class="ri-mic-line text-2xl" aria-hidden="true"></i>
    </button>
@endif
