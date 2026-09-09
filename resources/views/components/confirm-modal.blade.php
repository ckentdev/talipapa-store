<div
    id="confirm-modal"
    class="fixed inset-0 z-[100] hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="confirm-modal-title"
    aria-describedby="confirm-modal-message"
>
    <div data-confirm-backdrop class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl">
            <div class="border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-6 py-5">
                <div class="flex items-start gap-4">
                    <span
                        data-confirm-icon-wrap
                        class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-forest-600/10 text-forest-600"
                    >
                        <i data-confirm-icon class="ri-question-line text-xl" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h3 id="confirm-modal-title" data-confirm-title class="text-base font-bold text-gray-900"></h3>
                        <p id="confirm-modal-message" data-confirm-message class="mt-1 text-base text-gray-600"></p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4">
                <button
                    type="button"
                    data-confirm-cancel
                    class="rounded-lg border border-gray-200 px-4 py-2.5 text-base font-medium text-gray-700 transition hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    data-confirm-accept
                    class="rounded-lg bg-forest-600 px-4 py-2.5 text-base font-semibold text-white transition hover:bg-forest-700"
                >
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
