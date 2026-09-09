@extends('layouts.marketplace')

@section('title', 'My Addresses')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">My Addresses</h1>
    <a href="{{ route('customer.addresses.create') }}" class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">Add Address</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @forelse ($addresses as $address)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="font-semibold">{{ $address->label ?? 'Address' }}</h3>
                    @if ($address->is_default)
                        <span class="text-xs text-brand-600 font-medium">Default</span>
                    @endif
                    <p class="text-sm text-gray-600 mt-2">{{ $address->fullAddress() }}</p>
                </div>
                <div class="flex gap-2 text-sm">
                    <a href="{{ route('customer.addresses.edit', $address) }}" class="text-brand-600 hover:underline">Edit</a>
                    <form method="POST" action="{{ route('customer.addresses.destroy', $address) }}" onsubmit="return confirm('Delete this address?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-500 col-span-full">No addresses saved yet.</p>
    @endforelse
</div>
@endsection
