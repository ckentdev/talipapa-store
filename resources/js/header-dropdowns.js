import { initDropdowns } from 'flowbite';

function updateNotificationBadges(nextCount) {
    const label = nextCount >= 99 ? '99+' : String(nextCount);
    const badgeClass = 'absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white';
    const menuBadgeClass = 'absolute right-0 top-0 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold leading-none text-white ring-2 ring-white';

    ['notification-unread-badge', 'mobile-menu-notification-badge'].forEach((id, index) => {
        const badge = document.getElementById(id);
        const classes = index === 0 ? badgeClass : menuBadgeClass;

        if (badge) {
            badge.textContent = label;
            badge.classList.remove('hidden');
            return;
        }

        if (id === 'notification-unread-badge') {
            const button = document.getElementById('notification-bell-button');
            if (!button) {
                return;
            }

            const span = document.createElement('span');
            span.id = id;
            span.className = classes;
            span.textContent = label;
            button.appendChild(span);
            return;
        }

        const menuButton = document.querySelector('[data-drawer-toggle="marketplace-mobile-menu"]');
        if (!menuButton) {
            return;
        }

        const span = document.createElement('span');
        span.id = id;
        span.className = classes;
        span.textContent = label;
        menuButton.appendChild(span);
    });
}

export function initHeaderDropdowns() {
    initDropdowns();
}

export function initNotificationBadge() {
    window.addEventListener('repomart:notification', () => {
        const existingBadge = document.getElementById('notification-unread-badge')
            ?? document.getElementById('mobile-menu-notification-badge');
        const current = existingBadge
            ? parseInt(existingBadge.textContent.replace('+', ''), 10) || 0
            : 0;
        const next = Math.min(current + 1, 99);

        updateNotificationBadges(next);
    });
}
