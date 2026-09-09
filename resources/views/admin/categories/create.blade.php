@extends('layouts.admin')

@section('title', 'Add Category')

@section('content')
<form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="mx-auto max-w-lg space-y-4 rounded-xl border border-gray-200 bg-white p-6">
    @csrf
    <div>
        <label class="mb-1 block text-sm font-medium">Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Image (optional)</label>
        <x-file-input name="image" accept=".jpg,.jpeg,.png" hint="JPG or PNG." :preview="true" />
    </div>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked class="rounded text-brand-600"> Active</label>
    <div class="flex gap-3"><a href="{{ route('admin.categories.index') }}" class="rounded-lg border px-4 py-2 text-sm">Cancel</a><button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white">Create</button></div>
</form>
@endsection
