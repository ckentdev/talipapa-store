<div
    id="repomart-confirm-modal"
    tabindex="-1"
    aria-hidden="true"
    class="fixed inset-0 z-[60] hidden overflow-y-auto overflow-x-hidden p-4"
>
    <div class="fixed inset-0 bg-gray-900/50" data-confirm-dismiss></div>

    <div class="relative mx-auto flex min-h-full max-w-md items-center justify-center">
        <div class="relative w-full rounded-lg bg-white shadow-lg">
            <div class="p-6 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <svg class="h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                    </svg>
                </div>

                <h3 id="repomart-confirm-title" class="mb-2 text-lg font-semibold text-gray-900">Are you sure?</h3>
                <p id="repomart-confirm-message" class="mb-6 text-sm text-gray-500">This action cannot be undone.</p>

                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-center">
                    <button
                        type="button"
                        data-confirm-dismiss
                        class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        id="repomart-confirm-action"
                        class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300"
                    >
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('repomart-confirm-modal');
        const titleEl = document.getElementById('repomart-confirm-title');
        const messageEl = document.getElementById('repomart-confirm-message');
        const actionBtn = document.getElementById('repomart-confirm-action');
        let pendingCallback = null;

        function closeModal() {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            pendingCallback = null;
        }

        function openModal({ title, message, confirmText, onConfirm }) {
            titleEl.textContent = title ?? 'Are you sure?';
            messageEl.textContent = message ?? 'This action cannot be undone.';
            actionBtn.textContent = confirmText ?? 'Confirm';
            pendingCallback = typeof onConfirm === 'function' ? onConfirm : null;
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
        }

        modal.querySelectorAll('[data-confirm-dismiss]').forEach((el) => {
            el.addEventListener('click', closeModal);
        });

        actionBtn.addEventListener('click', () => {
            if (pendingCallback) pendingCallback();
            closeModal();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        window.addEventListener('repomart:confirm', (event) => {
            openModal(event.detail ?? {});
        });

        window.repomartConfirm = openModal;
    });
</script>
