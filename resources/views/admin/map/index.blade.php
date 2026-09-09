@extends('layouts.admin')

@section('title', 'Location Map')

@section('content')
<div id="admin-map" class="h-96 w-full overflow-hidden rounded-xl border border-gray-200 sm:h-[32rem]"></div>
<div class="mt-4 flex flex-wrap gap-4 text-sm">
    <span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-brand-600"></span> Stores</span>
    <span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-blue-600"></span> Riders</span>
    <span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-gray-500"></span> Customers</span>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const locations = @json($locations);
    const map = L.map('admin-map').setView([14.5995, 120.9842], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);

    const colors = { store: '#16a34a', rider: '#2563eb', customer: '#6b7280' };

    locations.forEach(loc => {
        L.circleMarker([loc.lat, loc.lng], {
            radius: 8,
            fillColor: colors[loc.type] || '#6b7280',
            color: '#fff',
            weight: 2,
            fillOpacity: 0.8,
        }).addTo(map).bindPopup(`<strong>${loc.name}</strong><br>${loc.type}`);
    });

    if (locations.length) {
        const bounds = L.latLngBounds(locations.map(l => [l.lat, l.lng]));
        map.fitBounds(bounds, { padding: [30, 30] });
    }
});
</script>
@endpush
