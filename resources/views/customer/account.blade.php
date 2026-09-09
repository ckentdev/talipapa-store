@extends('layouts.marketplace')

@section('title', 'Account & Permissions')

@section('content')
<h1 class="text-2xl font-bold mb-6">Account & Permissions</h1>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="font-semibold mb-4">Profile</h2>
        <dl class="text-sm space-y-2">
            <div><dt class="text-gray-500 inline">Name:</dt> <dd class="inline">{{ $user->name }}</dd></div>
            <div><dt class="text-gray-500 inline">Email:</dt> <dd class="inline">{{ $user->email }}</dd></div>
            <div><dt class="text-gray-500 inline">Phone:</dt> <dd class="inline">{{ $user->phone ?? '—' }}</dd></div>
        </dl>
        <a href="{{ route('profile.show') }}" class="inline-block mt-4 text-sm text-brand-600 hover:underline">Manage Jetstream profile</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
        <h2 class="font-semibold">Device Permissions</h2>

        <div class="flex items-center justify-between">
            <div>
                <p class="font-medium text-sm">Location</p>
                <p class="text-xs text-gray-500">For nearby stores and delivery</p>
            </div>
            <span class="text-sm text-gray-600" data-location-status>{{ $user->location_permission ? 'Allowed' : 'Not enabled' }}</span>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <p class="font-medium text-sm">Push Notifications</p>
                <p class="text-xs text-gray-500" data-push-status>{{ $user->push_permission ? 'Enabled' : 'Not enabled' }}</p>
            </div>
            <button type="button" data-enable-push class="text-sm px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 {{ $user->push_permission ? 'opacity-50 cursor-not-allowed' : '' }}" @disabled($user->push_permission)>Enable Push</button>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <p class="font-medium text-sm">Sound Alerts</p>
                <p class="text-xs text-gray-500" data-sound-status>{{ $user->sound_alerts_enabled ? 'Enabled' : 'Disabled' }}</p>
            </div>
            <button type="button" data-enable-sound class="text-sm px-4 py-2 border border-brand-600 text-brand-600 rounded-lg hover:bg-brand-50 {{ $user->sound_alerts_enabled ? 'opacity-50 cursor-not-allowed' : '' }}" @disabled($user->sound_alerts_enabled)>Enable Sound</button>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <p class="font-medium text-sm">Microphone</p>
                <p class="text-xs text-gray-500" data-mic-status>{{ $user->microphone_permission ? 'Allowed' : 'Not enabled' }}</p>
            </div>
            <button type="button" data-enable-microphone class="text-sm px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 {{ $user->microphone_permission ? 'opacity-50 cursor-not-allowed' : '' }}" @disabled($user->microphone_permission)>Allow Microphone</button>
        </div>
    </div>
</div>
@endsection
