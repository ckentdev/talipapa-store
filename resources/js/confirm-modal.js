const VARIANTS = {
    primary: {
        icon: 'ri-save-line',
        iconWrap: 'bg-forest-600/10 text-forest-600',
        button: 'bg-forest-600 hover:bg-forest-700 text-white',
    },
    danger: {
        icon: 'ri-delete-bin-line',
        iconWrap: 'bg-red-100 text-red-600',
        button: 'bg-red-600 hover:bg-red-700 text-white',
    },
};

function applyVariant(modal, variant) {
    const styles = VARIANTS[variant] ?? VARIANTS.primary;
    const iconWrap = modal.querySelector('[data-confirm-icon-wrap]');
    const icon = modal.querySelector('[data-confirm-icon]');
    const accept = modal.querySelector('[data-confirm-accept]');

    iconWrap.className = `inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl ${styles.iconWrap}`;
    icon.className = `${styles.icon} text-xl`;
    accept.className = `rounded-lg px-4 py-2.5 text-base font-semibold transition ${styles.button}`;
}

export function initConfirmModal() {
    const modal = document.getElementById('confirm-modal');
    if (!modal) {
        return;
    }

    const titleEl = modal.querySelector('[data-confirm-title]');
    const messageEl = modal.querySelector('[data-confirm-message]');
    const acceptBtn = modal.querySelector('[data-confirm-accept]');
    const cancelBtn = modal.querySelector('[data-confirm-cancel]');
    const backdrop = modal.querySelector('[data-confirm-backdrop]');

    let onConfirm = null;

    const close = () => {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        onConfirm = null;
    };

    const open = ({ title, message, confirmLabel, cancelLabel, variant, callback }) => {
        titleEl.textContent = title ?? 'Confirm action';
        messageEl.textContent = message ?? 'Are you sure you want to continue?';
        acceptBtn.textContent = confirmLabel ?? 'Confirm';
        cancelBtn.textContent = cancelLabel ?? 'Cancel';
        applyVariant(modal, variant ?? 'primary');
        onConfirm = callback ?? null;
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        acceptBtn.focus();
    };

    acceptBtn.addEventListener('click', () => {
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
        close();
    });

    cancelBtn.addEventListener('click', close);
    backdrop.addEventListener('click', close);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            close();
        }
    });

    document.querySelectorAll('[data-confirm-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const formSelector = trigger.dataset.confirmForm;
            const form = formSelector
                ? document.querySelector(formSelector)
                : trigger.closest('form');

            open({
                title: trigger.dataset.confirmTitle,
                message: trigger.dataset.confirmMessage,
                confirmLabel: trigger.dataset.confirmLabel,
                cancelLabel: trigger.dataset.confirmCancel,
                variant: trigger.dataset.confirmVariant,
                callback: () => {
                    if (form) {
                        form.requestSubmit();
                    }
                },
            });
        });
    });

    document.querySelectorAll('form[data-confirm-on-submit]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmConfirmed === '1') {
                delete form.dataset.confirmConfirmed;
                return;
            }

            event.preventDefault();

            open({
                title: form.dataset.confirmTitle,
                message: form.dataset.confirmMessage,
                confirmLabel: form.dataset.confirmLabel,
                cancelLabel: form.dataset.confirmCancel,
                variant: form.dataset.confirmVariant,
                callback: () => {
                    form.dataset.confirmConfirmed = '1';
                    form.requestSubmit();
                },
            });
        });
    });
}
