@extends('layouts.marketplace')

@section('title', 'Notifications')

@section('content')
<div class="mx-auto max-w-3xl">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Notifications</h1>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white divide-y divide-gray-100">
        @forelse ($notifications as $notification)
            <a
                href="{{ route('notifications.show', $notification->id) }}"
                @class([
                    'block px-4 py-4 transition hover:bg-gray-50 sm:px-6',
                    'bg-brand-50/40' => is_null($notification->read_at),
                ])
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900">
                            {{ data_get($notification->data, 'title', 'Notification') }}
                        </p>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ data_get($notification->data, 'message', '') }}
                        </p>
                        <p class="mt-2 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if (is_null($notification->read_at))
                        <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-brand-500" aria-label="Unread"></span>
                    @endif
                </div>
            </a>
        @empty
            <div class="px-6 py-12 text-center text-gray-500">
                No notifications yet.
            </div>
        @endforelse
    </div>

    @if ($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
