@extends('layouts.rider')

@section('title', 'Location')

@section('content')
<form method="POST" action="{{ route('rider.location.update') }}" class="mx-auto max-w-lg space-y-4 rounded-xl border border-gray-200 bg-white p-6">
    @csrf @method('PUT')
    <p class="text-sm text-gray-600">Update your current location so stores can find nearby riders.</p>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="mb-1 block text-sm font-medium">Latitude</label>
            <input type="number" name="latitude" value="{{ old('latitude', $profile?->latitude) }}" step="any" required class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">Longitude</label>
            <input type="number" name="longitude" value="{{ old('longitude', $profile?->longitude) }}" step="any" required class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
        </div>
    </div>
    <button type="button" data-use-location class="rounded-lg border border-brand-600 px-4 py-2 text-sm font-medium text-brand-600 hover:bg-brand-50">Use Current Location</button>
    <button type="submit" class="block w-full rounded-lg bg-brand-600 py-2.5 text-sm font-medium text-white hover:bg-brand-700">Save Location</button>
</form>
@endsection
