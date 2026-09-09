<form method="POST" action="{{ route('customer.checkout.process') }}">
    @csrf
    <input type="hidden" name="step" value="1">

    <ul class="divide-y divide-gray-100 rounded-xl border border-gray-100 bg-gray-50/40">
        @foreach ($cart->items as $item)
            <li class="flex gap-4 p-4">
                <x-product-image
                    :product="$item->product"
                    class="h-16 w-16 shrink-0 rounded-lg bg-white object-cover ring-1 ring-gray-100 sm:h-20 sm:w-20"
                />
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                    <p class="text-base text-gray-500">{{ $item->product->store?->store_name }}</p>
                    <p class="mt-1 text-base text-gray-600">
                        ₱{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}
                    </p>
                </div>
                <p class="shrink-0 font-semibold text-gray-900">₱{{ number_format($item->total(), 2) }}</p>
            </li>
        @endforeach
    </ul>

    <div class="mt-5 flex items-center justify-between rounded-xl border border-brand-100 bg-brand-50/50 px-4 py-3 text-base">
        <span class="font-medium text-gray-700">Cart subtotal</span>
        <span class="text-lg font-bold text-brand-700">₱{{ number_format($cart->subtotal(), 2) }}</span>
    </div>

    <x-customer.checkout.actions :step="1" class="mt-6" />
</form>
