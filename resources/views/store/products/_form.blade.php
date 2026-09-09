@props([
    'product' => null,
    'categories',
    'mode' => 'create',
])

@php
    $isEdit = $mode === 'edit';
    $inputClass = 'block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-base placeholder:text-gray-400 focus:border-brand-600 focus:ring-brand-600';
    $labelClass = 'mb-1 block text-base font-medium text-gray-700';

    $header = $isEdit
        ? ['icon' => 'ri-pencil-line', 'title' => 'Edit Product', 'subtitle' => 'Update listing, pricing, and stock.']
        : ['icon' => 'ri-file-list-3-line', 'title' => 'Product Details', 'subtitle' => 'Add a new item to your catalog.'];

    $formAction = $isEdit
        ? route('store.products.update', $product)
        : route('store.products.store');

    $confirmTitle = $isEdit ? 'Save changes' : 'Create product';
    $confirmMessage = $isEdit
        ? 'Update '.$product->name.' with your changes?'
        : 'Save this product to your catalog?';
    $confirmLabel = $isEdit ? 'Save' : 'Create';
    $submitLabel = $isEdit ? 'Save' : 'Create';
@endphp

<div class="mx-auto w-full max-w-4xl">
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-4 py-3 sm:px-5">
            <div class="flex min-w-0 items-center gap-3">
                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-forest-600/10 text-forest-600">
                    <i class="{{ $header['icon'] }} text-lg" aria-hidden="true"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="truncate text-base font-bold text-gray-900">{{ $header['title'] }}</h2>
                    <p class="truncate text-base text-gray-500">{{ $header['subtitle'] }}</p>
                </div>
            </div>
            <a href="{{ route('store.products') }}" class="inline-flex shrink-0 items-center gap-1 text-base font-medium text-gray-500 hover:text-brand-600">
                <i class="ri-arrow-left-line" aria-hidden="true"></i>
                Back
            </a>
        </div>

        <form
            method="POST"
            action="{{ $formAction }}"
            enctype="multipart/form-data"
            class="p-4 sm:p-5"
            data-confirm-on-submit
            data-confirm-title="{{ $confirmTitle }}"
            data-confirm-message="{{ $confirmMessage }}"
            data-confirm-label="{{ $confirmLabel }}"
            data-confirm-variant="primary"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                {{-- Preview --}}
                <aside class="md:col-span-1">
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                        <div class="aspect-[4/3] overflow-hidden bg-gray-100">
                            @if ($isEdit)
                                <x-product-image :product="$product" class="h-full w-full object-cover" />
                            @else
                                <div class="flex h-full items-center justify-center text-gray-400">
                                    <i class="ri-image-add-line text-3xl" aria-hidden="true"></i>
                                </div>
                            @endif
                        </div>

                        @if ($isEdit)
                            <div class="flex flex-wrap gap-1.5 border-t border-gray-200 p-2.5">
                                <span @class([
                                    'inline-flex rounded-full px-2 py-0.5 text-base font-semibold',
                                    'bg-green-100 text-green-800' => $product->is_available,
                                    'bg-gray-100 text-gray-600' => ! $product->is_available,
                                ])>{{ $product->is_available ? 'Available' : 'Hidden' }}</span>
                                <span class="inline-flex rounded-full bg-brand-50 px-2 py-0.5 text-base font-semibold text-brand-700">₱{{ number_format($product->price, 2) }}</span>
                                <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-base font-semibold text-gray-700">{{ $product->stock }} stock</span>
                                @if (filled($product->barcode))
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-base font-semibold text-gray-700">
                                        <i class="ri-barcode-line text-sm" aria-hidden="true"></i>
                                        {{ $product->barcode }}
                                    </span>
                                @endif
                            </div>
                            <a
                                href="{{ route('products.show', $product) }}"
                                target="_blank"
                                rel="noopener"
                                class="flex items-center justify-center gap-1.5 border-t border-gray-200 px-2.5 py-2 text-base font-medium text-gray-600 hover:text-brand-700"
                            >
                                <i class="ri-external-link-line" aria-hidden="true"></i>
                                Public listing
                            </a>
                        @endif
                    </div>
                </aside>

                {{-- Fields --}}
                <div class="space-y-3 md:col-span-2">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="category_id" class="{{ $labelClass }}">Category</label>
                            <select id="category_id" name="category_id" required class="{{ $inputClass }}">
                                @unless ($isEdit)
                                    <option value="">Select category</option>
                                @endunless
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $product?->category_id) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="category_id" class="mt-1" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="name" class="{{ $labelClass }}">Product name</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $product?->name) }}" required placeholder="e.g. Fresh Tomatoes 1kg" class="{{ $inputClass }}">
                            <x-input-error for="name" class="mt-1" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="barcode" class="{{ $labelClass }}">Barcode <span class="font-normal text-gray-400">(optional)</span></label>
                            <input
                                id="barcode"
                                type="text"
                                name="barcode"
                                value="{{ old('barcode', $product?->barcode) }}"
                                inputmode="numeric"
                                autocomplete="off"
                                placeholder="e.g. 4800123456789"
                                class="{{ $inputClass }}"
                            >
                            <p class="mt-1 text-sm text-gray-500">For POS scanning and inventory tracking. Must be unique within your store.</p>
                            <x-input-error for="barcode" class="mt-1" />
                        </div>

                        <div>
                            <label for="price" class="{{ $labelClass }}">Price (₱)</label>
                            <input id="price" type="number" name="price" value="{{ old('price', $product?->price) }}" step="0.01" min="0" required placeholder="0.00" class="{{ $inputClass }}">
                            <x-input-error for="price" class="mt-1" />
                        </div>

                        <div>
                            <label for="stock" class="{{ $labelClass }}">Stock</label>
                            <input id="stock" type="number" name="stock" value="{{ old('stock', $product?->stock ?? 0) }}" min="0" required placeholder="0" class="{{ $inputClass }}">
                            <x-input-error for="stock" class="mt-1" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="description" class="{{ $labelClass }}">Description</label>
                            <textarea id="description" name="description" rows="2" placeholder="Short product description..." class="{{ $inputClass }}">{{ old('description', $product?->description) }}</textarea>
                            <x-input-error for="description" class="mt-1" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="image" class="{{ $labelClass }}">{{ $isEdit ? 'Replace image' : 'Image' }}</label>
                            <x-file-input
                                id="image"
                                name="image"
                                accept=".jpg,.jpeg,.png"
                                :preview="true"
                                hint="JPG or PNG, maximum 5MB."
                            />
                            <x-input-error for="image" class="mt-1" />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                                <input type="checkbox" name="is_available" value="1" @checked(old('is_available', $product?->is_available ?? true)) class="rounded border-gray-300 text-forest-600 focus:ring-forest-600">
                                <span class="text-base text-gray-700">Available for purchase</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-gray-100 pt-3">
                        <a href="{{ route('store.products') }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50">
                            <i class="ri-close-line" aria-hidden="true"></i>
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-forest-600 px-3 py-2 text-base font-semibold text-white hover:bg-forest-700">
                            <i class="{{ $isEdit ? 'ri-save-line' : 'ri-add-line' }}" aria-hidden="true"></i>
                            {{ $submitLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
