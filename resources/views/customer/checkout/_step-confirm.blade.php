@php
    $selectedAddress = $addresses->firstWhere('id', $checkout['address_id'] ?? null);
    $paymentLabel = \App\Support\CheckoutPaymentSummary::label($checkout);
    $paymentMethod = \App\Enums\PaymentMethod::tryFromStored($checkout['payment_method'] ?? null);
@endphp

<div class="space-y-4">
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-gray-100 bg-gray-50/60 p-4">
            <p class="flex items-center gap-2 text-base font-bold uppercase tracking-wider text-gray-400">
                <i class="ri-user-3-line" aria-hidden="true"></i>
                Contact
            </p>
            <p class="mt-2 text-base font-semibold text-gray-900">{{ $checkout['customer']['name'] ?? auth()->user()->name }}</p>
            <p class="text-base text-gray-600">{{ $checkout['customer']['phone'] ?? auth()->user()->phone }}</p>
        </div>

        <div class="rounded-xl border border-gray-100 bg-gray-50/60 p-4">
            <p class="flex items-center gap-2 text-base font-bold uppercase tracking-wider text-gray-400">
                <i class="ri-bank-card-line" aria-hidden="true"></i>
                Payment
            </p>
            <p class="mt-2 text-base font-semibold text-gray-900">{{ $paymentLabel }}</p>
            @if ($paymentMethod === \App\Enums\PaymentMethod::Cod)
                <p class="text-base text-gray-600">Pay when your order arrives.</p>
            @endif
        </div>
    </div>

    <div class="rounded-xl border border-gray-100 bg-gray-50/60 p-4">
        <p class="flex items-center gap-2 text-base font-bold uppercase tracking-wider text-gray-400">
            <i class="ri-map-pin-2-line" aria-hidden="true"></i>
            Delivery address
        </p>
        <p class="mt-2 text-base font-semibold text-gray-900">{{ $selectedAddress?->label ?? 'Address' }}</p>
        <p class="text-base leading-relaxed text-gray-600">{{ $selectedAddress?->fullAddress() }}</p>
    </div>

    <div class="rounded-xl border border-gray-100 bg-white p-4">
        <p class="mb-3 text-base font-bold uppercase tracking-wider text-gray-400">Items</p>
        <ul class="divide-y divide-gray-100">
            @foreach ($cart->items as $item)
                <li class="flex items-center justify-between gap-3 py-2.5 text-base">
                    <span class="text-gray-700">{{ $item->product->name }} × {{ $item->quantity }}</span>
                    <span class="font-medium text-gray-900">₱{{ number_format($item->total(), 2) }}</span>
                </li>
            @endforeach
        </ul>
        <dl class="mt-3 space-y-1.5 border-t border-gray-100 pt-3 text-base">
            <div class="flex justify-between text-gray-600">
                <dt>Subtotal</dt>
                <dd class="font-medium text-gray-900">₱{{ number_format($cart->subtotal(), 2) }}</dd>
            </div>
            <div class="flex justify-between text-gray-600">
                <dt>Delivery fee</dt>
                <dd class="font-medium text-gray-900">₱{{ number_format($deliveryFee, 2) }}</dd>
            </div>
            <div class="flex justify-between border-t border-gray-100 pt-2 text-base font-bold text-gray-900">
                <dt>Total</dt>
                <dd class="text-brand-600">₱{{ number_format($cart->subtotal() + $deliveryFee, 2) }}</dd>
            </div>
        </dl>
    </div>
</div>

<form method="POST" action="{{ route('customer.checkout.process') }}" class="mt-6">
    @csrf
    <input type="hidden" name="step" value="5">

    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/30">
        <input
            type="checkbox"
            name="confirm"
            value="1"
            required
            class="mt-0.5 rounded border-gray-300 text-brand-600 focus:ring-brand-600"
        >
        <span>
            <span class="block text-base font-semibold text-gray-900">I confirm this order is correct</span>
            <span class="mt-0.5 block text-base text-gray-500">By placing this order, you agree to our delivery and payment terms.</span>
        </span>
    </label>

    <x-customer.checkout.actions :step="5" submit-label="Place Order" class="mt-5" />
</form>
