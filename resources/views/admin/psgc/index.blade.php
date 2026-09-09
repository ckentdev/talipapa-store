@extends('layouts.admin')

@section('title', 'PSGC Data')

@section('content')
<p class="mb-4 text-sm text-gray-600">Philippine Standard Geographic Code reference data used for address forms.</p>
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Code</th><th class="px-4 py-3 text-left">Region</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($regions as $region)
                <tr><td class="px-4 py-3 font-mono text-xs">{{ $region->code }}</td><td class="px-4 py-3">{{ $region->name }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
<p class="mt-4 text-sm text-gray-500">{{ $regions->count() }} regions loaded. Province/city/barangay data is available via API dropdowns.</p>
@endsection
