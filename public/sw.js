self.addEventListener('push', (event) => {
    const data = event.data ? event.data.json() : {};
    const title = data.title || 'RepoMart';
    const options = {
        body: data.body || data.message || 'You have a new notification',
        icon: '/favicon.ico',
        data: data,
    };
    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.action || '/';
    event.waitUntil(clients.openWindow(url));
});
