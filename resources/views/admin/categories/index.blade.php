@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="mb-4 flex justify-between">
    <p class="text-sm text-gray-500">{{ $categories->total() }} categories</p>
    <a href="{{ route('admin.categories.create') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white">Add Category</a>
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Products</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3"></th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($categories as $category)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                    <td class="px-4 py-3">{{ $category->products_count }}</td>
                    <td class="px-4 py-3">{{ $category->is_active ? 'Active' : 'Inactive' }}</td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-brand-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:underline">Delete</button></form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $categories->links() }}</div>
@endsection
