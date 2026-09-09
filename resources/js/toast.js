const TOAST_STYLES = {
    success: {
        border: 'border-forest-200',
        iconWrap: 'bg-forest-600 text-white',
        icon: 'ri-checkbox-circle-line',
        title: 'Success',
        titleClass: 'text-forest-800',
    },
    error: {
        border: 'border-red-200',
        iconWrap: 'bg-red-500 text-white',
        icon: 'ri-error-warning-line',
        title: 'Error',
        titleClass: 'text-red-800',
    },
    warning: {
        border: 'border-orange-200',
        iconWrap: 'bg-orange-500 text-white',
        icon: 'ri-alert-line',
        title: 'Attention',
        titleClass: 'text-orange-800',
    },
    info: {
        border: 'border-brand-200',
        iconWrap: 'bg-brand-600 text-white',
        icon: 'ri-information-line',
        title: 'Notice',
        titleClass: 'text-gray-900',
    },
};

let hideTimer = null;

function hideToast() {
    const toast = document.getElementById('repomart-toast');

    if (! toast) {
        return;
    }

    toast.classList.add('translate-y-2', 'opacity-0', 'pointer-events-none');

    window.setTimeout(() => {
        toast.classList.add('hidden');
    }, 300);

    if (hideTimer) {
        clearTimeout(hideTimer);
        hideTimer = null;
    }
}

function showToast(message, type = 'info') {
    const toast = document.getElementById('repomart-toast');
    const inner = document.getElementById('repomart-toast-inner');
    const iconWrap = document.getElementById('repomart-toast-icon');
    const icon = document.getElementById('repomart-toast-icon-i');
    const titleEl = document.getElementById('repomart-toast-title');
    const messageEl = document.getElementById('repomart-toast-message');
    const closeBtn = document.getElementById('repomart-toast-close');

    if (! toast || ! inner || ! iconWrap || ! icon || ! titleEl || ! messageEl) {
        return;
    }

    const style = TOAST_STYLES[type] ?? TOAST_STYLES.info;

    inner.className = `pointer-events-auto flex items-start gap-3 rounded-xl border bg-white p-4 shadow-lg ${style.border}`;
    iconWrap.className = `inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full ${style.iconWrap}`;
    icon.className = `${style.icon} text-lg`;
    titleEl.textContent = style.title;
    titleEl.className = `text-sm font-semibold ${style.titleClass}`;
    titleEl.classList.remove('hidden');
    messageEl.textContent = message;

    toast.classList.remove('hidden', 'translate-y-2', 'opacity-0', 'pointer-events-none');

    closeBtn?.replaceWith(closeBtn.cloneNode(true));
    document.getElementById('repomart-toast-close')?.addEventListener('click', hideToast);

    if (hideTimer) {
        clearTimeout(hideTimer);
    }

    hideTimer = window.setTimeout(hideToast, 5000);
}

export function initToast() {
    document.getElementById('repomart-toast-close')?.addEventListener('click', hideToast);

    window.addEventListener('repomart:toast', (event) => {
        const { message, type } = event.detail ?? {};

        if (message) {
            showToast(message, type);
        }
    });

    window.showRepomartToast = showToast;
}

export { showToast, hideToast };
