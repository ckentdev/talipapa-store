export function initPushNotifications() {
    document.querySelectorAll('[data-enable-push]').forEach((button) => {
        button.addEventListener('click', () => enablePush(button));
    });
}

async function enablePush(button) {
    if (!('Notification' in window) || !('serviceWorker' in navigator)) {
        alert('Push notifications are not supported in this browser.');
        return;
    }

    const permission = await Notification.requestPermission();
    updatePushStatus(button, permission);

    if (permission !== 'granted') {
        alert('Notification permission was denied. You will receive in-app notifications instead.');
        await savePermission('push', false);
        return;
    }

    await savePermission('push', true);

    try {
        const registration = await navigator.serviceWorker.register('/sw.js');
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(window.VAPID_PUBLIC_KEY),
        });

        await window.axios.post('/push/subscribe', subscription.toJSON());
        button.textContent = 'Notifications Enabled';
        button.disabled = true;
    } catch (e) {
        console.error('Push subscription failed:', e);
    }
}

function updatePushStatus(button, permission) {
    const statusEl = document.querySelector('[data-push-status]');
    if (statusEl) {
        statusEl.textContent = permission === 'granted' ? 'Enabled' : permission === 'denied' ? 'Denied' : 'Not enabled';
    }
}

async function savePermission(permission, granted) {
    await window.axios.post('/account/permissions', { permission, granted });
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    return Uint8Array.from([...rawData].map((char) => char.charCodeAt(0)));
}
