@props(['product'])

@php
    $maxQty = min(99, (int) $product->stock);
@endphp

<div {{ $attributes->merge(['class' => 'border-t border-gray-100 bg-gray-50/40 px-5 py-5 sm:px-6 sm:py-6']) }}>
    <form method="POST" action="{{ route('cart.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <div>
            <label for="product-quantity" class="mb-2 block text-base font-medium text-gray-700">Quantity</label>
            <div
                class="inline-flex items-center overflow-hidden rounded-xl border border-gray-200 bg-white"
                data-product-qty-stepper
                data-max="{{ $maxQty }}"
            >
                <button
                    type="button"
                    data-qty-minus
                    class="flex h-11 w-11 items-center justify-center text-gray-600 transition hover:bg-gray-50 hover:text-brand-600"
                    aria-label="Decrease quantity"
                >
                    <i class="ri-subtract-line text-lg" aria-hidden="true"></i>
                </button>

                <input
                    id="product-quantity"
                    type="number"
                    name="quantity"
                    value="1"
                    min="1"
                    max="{{ $maxQty }}"
                    data-qty-input
                    class="h-11 w-16 border-x border-gray-200 bg-transparent text-center text-base font-semibold text-gray-900 focus:border-brand-600 focus:ring-brand-600"
                >

                <button
                    type="button"
                    data-qty-plus
                    class="flex h-11 w-11 items-center justify-center text-gray-600 transition hover:bg-gray-50 hover:text-brand-600 disabled:cursor-not-allowed disabled:opacity-40"
                    aria-label="Increase quantity"
                >
                    <i class="ri-add-line text-lg" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <p class="text-base text-gray-600">
            Subtotal:
            <span class="font-semibold text-gray-900" data-product-line-total>
                ₱{{ number_format($product->price, 2) }}
            </span>
        </p>

        <button
            type="submit"
            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-3.5 text-base font-semibold text-white transition hover:bg-brand-700"
        >
            <i class="ri-shopping-cart-2-line text-lg" aria-hidden="true"></i>
            Add to cart
        </button>
    </form>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-product-qty-stepper]').forEach((stepper) => {
                    const input = stepper.querySelector('[data-qty-input]');
                    const minus = stepper.querySelector('[data-qty-minus]');
                    const plus = stepper.querySelector('[data-qty-plus]');
                    const lineTotal = stepper.closest('form')?.querySelector('[data-product-line-total]');
                    const max = Number(stepper.dataset.max ?? 99);
                    const unitPrice = Number(stepper.closest('[data-product-purchase]')?.dataset.unitPrice ?? 0);

                    const clamp = (value) => Math.min(max, Math.max(1, value));

                    const sync = () => {
                        const quantity = clamp(Number(input.value) || 1);
                        input.value = quantity;
                        plus.disabled = quantity >= max;

                        if (lineTotal && unitPrice > 0) {
                            lineTotal.textContent = `₱${(unitPrice * quantity).toLocaleString('en-PH', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            })}`;
                        }
                    };

                    minus?.addEventListener('click', () => {
                        input.value = clamp(Number(input.value) - 1);
                        sync();
                    });

                    plus?.addEventListener('click', () => {
                        input.value = clamp(Number(input.value) + 1);
                        sync();
                    });

                    input?.addEventListener('change', sync);
                    sync();
                });
            });
        </script>
    @endpush
@endonce
