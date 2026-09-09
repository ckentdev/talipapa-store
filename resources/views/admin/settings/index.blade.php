@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="mx-auto max-w-lg space-y-4 rounded-xl border border-gray-200 bg-white p-6">
    @csrf @method('PUT')
    <div>
        <label class="mb-1 block text-sm font-medium">Platform Name</label>
        <input type="text" name="platform_name" value="{{ old('platform_name', $settings['platform_name']) }}" required class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Support Email</label>
        <input type="email" name="support_email" value="{{ old('support_email', $settings['support_email']) }}" required class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Delivery Fee (₱)</label>
        <input type="number" name="delivery_fee" value="{{ old('delivery_fee', $settings['delivery_fee']) }}" step="0.01" min="0" required class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Minimum Order Amount (₱)</label>
        <input type="number" name="min_order_amount" value="{{ old('min_order_amount', $settings['min_order_amount']) }}" step="0.01" min="0" required class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
    </div>
    <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700">Save Settings</button>
</form>
@endsection
