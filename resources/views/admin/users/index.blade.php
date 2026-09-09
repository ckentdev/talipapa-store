@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<form method="GET" class="mb-4 flex flex-wrap gap-3">
    <select name="role" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <option value="">All roles</option>
        @foreach (['customer', 'store_owner', 'rider', 'admin'] as $role)
            <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
        @endforeach
    </select>
    <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <option value="">All statuses</option>
        @foreach (['active', 'inactive', 'suspended'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white">Filter</button>
</form>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Role</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3"></th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($users as $user)
                <tr>
                    <td class="px-4 py-3"><p class="font-medium">{{ $user->name }}</p><p class="text-gray-500">{{ $user->email }}</p></td>
                    <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $user->role->value) }}</td>
                    <td class="px-4 py-3 capitalize">{{ $user->status->value }}</td>
                    <td class="px-4 py-3 text-right"><a href="{{ route('admin.users.show', $user) }}" class="text-brand-600 hover:underline">View</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $users->links() }}</div>
@endsection
