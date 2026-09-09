<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesDocumentUpload;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use HandlesDocumentUpload;

    public function index(): View
    {
        $categories = Category::query()->withCount('products')->orderBy('name')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'is_active' => 'boolean',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->uploadDocument($request->file('image'), 'categories');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        unset($validated['image']);

        Category::query()->create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'is_active' => 'boolean',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->uploadDocument($request->file('image'), 'categories');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        unset($validated['image']);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted.');
    }
}
