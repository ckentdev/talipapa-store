@extends('layouts.marketplace')

@section('title', 'Stores')

@section('content')
<h1 class="text-2xl font-bold mb-6">Stores</h1>

<form method="GET" class="mb-6 flex gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search stores..." class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
    <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700">Search</button>
</form>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($stores as $store)
        <a href="{{ route('stores.show', $store) }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition block">
            <h2 class="font-semibold text-lg">{{ $store->store_name }}</h2>
            <p class="text-sm text-gray-500 mt-2 line-clamp-3">{{ $store->description }}</p>
            @if ($store->addresses->first())
                <p class="text-xs text-gray-400 mt-3">{{ $store->addresses->first()->fullAddress() }}</p>
            @endif
        </a>
    @empty
        <p class="text-gray-500 col-span-full">No stores found.</p>
    @endforelse
</div>

<div class="mt-8">{{ $stores->links() }}</div>
@endsection
