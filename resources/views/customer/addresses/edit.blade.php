@extends('layouts.marketplace')

@section('title', 'Edit Address')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Address</h1>

<form method="POST" action="{{ route('customer.addresses.update', $address) }}" class="bg-white rounded-xl border border-gray-200 p-6 max-w-2xl">
    @csrf
    @method('PUT')
    @include('partials.address-form', ['regions' => $regions, 'address' => $address])
    <div class="mt-6 flex gap-4">
        <button type="submit" class="px-6 py-3 bg-brand-600 text-white rounded-lg hover:bg-brand-700">Update Address</button>
        <a href="{{ route('customer.addresses.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
    </div>
</form>
@endsection
