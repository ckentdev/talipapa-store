@extends('layouts.rider')

@section('title', 'Account')

@section('content')
<form method="POST" action="{{ route('rider.account.update') }}" class="mx-auto max-w-lg space-y-4 rounded-xl border border-gray-200 bg-white p-6">
    @csrf @method('PUT')
    <div>
        <label class="mb-1 block text-sm font-medium">Name</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Email</label>
        <input type="email" value="{{ $user->email }}" disabled class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-500">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Vehicle Type</label>
        <select name="vehicle_type" required class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
            @foreach (['Motorcycle', 'Bicycle', 'Tricycle', 'Car'] as $type)
                <option value="{{ $type }}" @selected(old('vehicle_type', $profile?->vehicle_type) === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Plate Number</label>
        <input type="text" name="plate_number" value="{{ old('plate_number', $profile?->plate_number) }}" class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
    </div>
    @if ($profile)
        <div class="rounded-lg bg-gray-50 p-3 text-sm">
            <p>Status: <strong class="capitalize">{{ $profile->status->value }}</strong></p>
        </div>
    @endif
    <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700">Save Changes</button>
</form>
@endsection
