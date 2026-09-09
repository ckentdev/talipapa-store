import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

export function initEcho() {
    const userId = document.body.dataset.userId;
    if (!userId || !import.meta.env.VITE_PUSHER_APP_KEY) {
        return;
    }

    if (window.Echo) {
        window.Echo.leave(`private-App.Models.User.${userId}`);
        window.Echo.disconnect();
    }

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
        },
    });

    window.Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {
            window.dispatchEvent(new CustomEvent('repomart:notification', { detail: notification }));

            const message = notification.message ?? notification.title ?? 'New notification';

            if (typeof window.showRepomartToast === 'function') {
                window.showRepomartToast(message, 'info');
            }
        });
}
