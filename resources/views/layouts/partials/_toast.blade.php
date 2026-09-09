<div
    id="repomart-toast"
    class="pointer-events-none fixed bottom-20 left-4 right-4 z-50 mx-auto hidden max-w-md translate-y-2 opacity-0 transition-all duration-300 lg:bottom-6 lg:left-auto lg:right-6"
    role="alert"
    aria-live="polite"
>
    <div id="repomart-toast-inner" class="pointer-events-auto flex items-start gap-3 rounded-xl border bg-white p-4 shadow-lg">
        <span id="repomart-toast-icon" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full">
            <i id="repomart-toast-icon-i" class="text-lg" aria-hidden="true"></i>
        </span>
        <div class="min-w-0 flex-1 pt-0.5">
            <p id="repomart-toast-title" class="hidden text-sm font-semibold"></p>
            <p id="repomart-toast-message" class="text-sm leading-relaxed text-gray-700"></p>
        </div>
        <button
            type="button"
            id="repomart-toast-close"
            class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
            aria-label="Close"
        >
            <i class="ri-close-line text-lg" aria-hidden="true"></i>
        </button>
    </div>
</div>

{{-- Hidden flash payloads for toast mirroring (store / marketplace app) --}}
@if (session('success'))
    <span data-flash-success class="hidden">{{ session('success') }}</span>
@endif
@if (session('error'))
    <span data-flash-error class="hidden">{{ session('error') }}</span>
@endif
@if (session('warning'))
    <span data-flash-warning class="hidden">{{ session('warning') }}</span>
@endif
