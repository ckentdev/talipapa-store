@extends('layouts.admin')

@section('title', $user->name)

@section('content')
<a href="{{ route('admin.users') }}" class="mb-4 inline-block text-sm text-brand-600 hover:underline">← Back to users</a>

<div class="grid gap-6 lg:grid-cols-2">
    <div class="rounded-xl border border-gray-200 bg-white p-4">
        <h3 class="mb-3 font-semibold">Profile</h3>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd>{{ $user->email }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Phone</dt><dd>{{ $user->phone ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Role</dt><dd class="capitalize">{{ str_replace('_', ' ', $user->role->value) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Joined</dt><dd>{{ $user->created_at->format('M d, Y') }}</dd></div>
        </dl>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4">
        <h3 class="mb-3 font-semibold">Update Status</h3>
        <form method="POST" action="{{ route('admin.users.update-status', $user) }}" class="flex gap-3">
            @csrf @method('PATCH')
            <select name="status" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                @foreach (['active', 'inactive', 'suspended'] as $status)
                    <option value="{{ $status }}" @selected($user->status->value === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white">Update</button>
        </form>
    </div>
</div>
@endsection
