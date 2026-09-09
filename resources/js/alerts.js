export function initAlerts() {
    document.querySelectorAll('[data-alert-dismiss]').forEach((button) => {
        button.addEventListener('click', () => {
            const alert = button.closest('[data-alert]');
            alert?.remove();
        });
    });

    document.querySelectorAll('[data-alert][data-auto-dismiss]').forEach((alert) => {
        const delay = Number(alert.dataset.autoDismiss) || 8000;
        window.setTimeout(() => {
            alert.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            window.setTimeout(() => alert.remove(), 300);
        }, delay);
    });

    const flash = {
        success: document.querySelector('[data-flash-success]')?.textContent?.trim(),
        error: document.querySelector('[data-flash-error]')?.textContent?.trim(),
        warning: document.querySelector('[data-flash-warning]')?.textContent?.trim(),
    };

    const type = flash.success ? 'success' : flash.error ? 'error' : flash.warning ? 'warning' : null;
    const message = flash.success || flash.error || flash.warning;

    if (type && message) {
        window.dispatchEvent(new CustomEvent('repomart:toast', { detail: { message, type } }));
    }
}
