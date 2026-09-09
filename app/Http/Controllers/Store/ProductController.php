<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Concerns\HandlesDocumentUpload;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    use HandlesDocumentUpload;

    public function index(Request $request): View
    {
        $store = $request->user()->storeProfile;

        $products = $store->products()
            ->with('category')
            ->latest()
            ->paginate(18);

        $statsRow = $store->products()
            ->toBase()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available')
            ->selectRaw('SUM(CASE WHEN is_available = 0 THEN 1 ELSE 0 END) as hidden')
            ->selectRaw('SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) as out_of_stock')
            ->selectRaw('SUM(CASE WHEN stock > 0 AND stock <= 5 THEN 1 ELSE 0 END) as low_stock')
            ->selectRaw('COALESCE(SUM(price * stock), 0) as inventory_value')
            ->first();

        $productStats = [
            'total' => (int) ($statsRow->total ?? 0),
            'available' => (int) ($statsRow->available ?? 0),
            'hidden' => (int) ($statsRow->hidden ?? 0),
            'out_of_stock' => (int) ($statsRow->out_of_stock ?? 0),
            'low_stock' => (int) ($statsRow->low_stock ?? 0),
            'inventory_value' => (float) ($statsRow->inventory_value ?? 0),
        ];

        return view('store.products.index', compact('products', 'store', 'productStats'));
    }

    public function create(Request $request): View
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();

        return view('store.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $store = $request->user()->storeProfile;

        $validated = $request->validate($this->productRules($store->id));

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->uploadDocument($request->file('image'), 'products');
        } else {
            $validated['image_path'] = 'img/placeholders/product.svg';
        }

        $validated['store_profile_id'] = $store->id;
        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['barcode'] = filled($validated['barcode'] ?? null) ? $validated['barcode'] : null;
        unset($validated['image']);

        Product::query()->create($validated);

        return redirect()->route('store.products')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Request $request, Product $product): View
    {
        abort_unless($product->store_profile_id === $request->user()->storeProfile->id, 403);

        $product->load('category');

        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();

        return view('store.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->store_profile_id === $request->user()->storeProfile->id, 403);

        $validated = $request->validate($this->productRules($product->store_profile_id, $product->id));

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->uploadDocument($request->file('image'), 'products');
        }

        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['barcode'] = filled($validated['barcode'] ?? null) ? $validated['barcode'] : null;
        unset($validated['image']);

        $product->update($validated);

        return redirect()->route('store.products')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->store_profile_id === $request->user()->storeProfile->id, 403);

        $product->delete();

        return redirect()->route('store.products')
            ->with('success', 'Product deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function productRules(int $storeProfileId, ?int $productId = null): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'barcode' => [
                'nullable',
                'string',
                'max:64',
                Rule::unique('products', 'barcode')
                    ->where('store_profile_id', $storeProfileId)
                    ->ignore($productId),
            ],
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'is_available' => 'boolean',
        ];
    }
}
