@auth
    @php
        $unreadCount = auth()->user()->unreadNotifications()->count();
    @endphp

    <div class="relative">
        <button
            id="notification-bell-button"
            type="button"
            data-dropdown-toggle="notification-dropdown"
            data-dropdown-placement="bottom-end"
            class="relative flex h-10 w-10 items-center justify-center rounded-full text-forest-600 transition hover:bg-avocado-50 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-200"
            aria-label="Notifications"
        >
            <i class="ri-notification-3-line text-xl" aria-hidden="true"></i>

            @if ($unreadCount > 0)
                <span
                    id="notification-unread-badge"
                    class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white"
                >
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </button>

        <div
            id="notification-dropdown"
            class="z-50 hidden w-80 divide-y divide-gray-100 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg sm:w-96"
        >
            <div class="px-4 py-3">
                <p class="text-sm font-semibold text-gray-900">Notifications</p>
                <p class="text-xs text-gray-500">{{ $unreadCount }} unread</p>
            </div>

            <div class="max-h-72 overflow-y-auto">
                @forelse (auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                    <a
                        href="{{ route('notifications.show', $notification->id) }}"
                        @class([
                            'block px-4 py-3 transition hover:bg-gray-50',
                            'bg-brand-50/50' => is_null($notification->read_at),
                        ])
                    >
                        <p class="truncate text-sm font-medium text-gray-900">
                            {{ data_get($notification->data, 'title', 'Notification') }}
                        </p>
                        <p class="mt-0.5 truncate text-xs text-gray-500">
                            {{ data_get($notification->data, 'message', '') }}
                        </p>
                        <p class="mt-1 text-[11px] text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </a>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-gray-500">
                        No notifications yet.
                    </div>
                @endforelse
            </div>

            <div class="px-4 py-3">
                <a href="{{ route('notifications.index') }}" class="block text-center text-sm font-semibold text-brand-600 hover:text-brand-700">
                    View all notifications
                </a>
            </div>
        </div>
    </div>
@endauth
