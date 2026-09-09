@if ($addresses->isEmpty())
    <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50/60 px-6 py-10 text-center">
        <span class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-600">
            <i class="ri-map-pin-add-line text-2xl" aria-hidden="true"></i>
        </span>
        <h3 class="mt-4 text-base font-semibold text-gray-900">No saved addresses</h3>
        <p class="mx-auto mt-1 max-w-sm text-base text-gray-500">Add a delivery address to continue with your order.</p>
        <a
            href="{{ route('customer.addresses.create') }}"
            class="mt-5 inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2.5 text-base font-semibold text-white hover:bg-brand-700"
        >
            <i class="ri-add-line" aria-hidden="true"></i>
            Add address
        </a>
    </div>

    <div class="mt-6">
        <x-customer.checkout.actions :step="3" />
    </div>
@else
    <form method="POST" action="{{ route('customer.checkout.process') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="step" value="3">

        <div class="space-y-3">
            @foreach ($addresses as $address)
                @php
                    $isSelected = old('address_id', $checkout['address_id'] ?? $addresses->firstWhere('is_default', true)?->id) == $address->id;
                @endphp
                <label @class([
                    'group flex cursor-pointer items-start gap-4 rounded-xl border-2 p-4 transition hover:shadow-sm',
                    'border-brand-600 bg-brand-50/30 ring-1 ring-brand-600/10' => $isSelected,
                    'border-gray-200 bg-white hover:border-brand-300' => ! $isSelected,
                ])>
                    <input
                        type="radio"
                        name="address_id"
                        value="{{ $address->id }}"
                        @checked($isSelected)
                        class="mt-1 text-brand-600 focus:ring-brand-600"
                    >
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-semibold text-gray-900">{{ $address->label ?? 'Address' }}</span>
                            @if ($address->is_default)
                                <span class="rounded-full bg-brand-100 px-2 py-0.5 text-sm font-bold uppercase tracking-wide text-brand-700">Default</span>
                            @endif
                        </div>
                        <p class="mt-1.5 text-base leading-relaxed text-gray-600">{{ $address->fullAddress() }}</p>
                    </div>
                    <span @class([
                        'flex h-9 w-9 shrink-0 items-center justify-center rounded-full transition',
                        'bg-brand-600 text-white' => $isSelected,
                        'bg-gray-100 text-gray-400 group-hover:bg-brand-50 group-hover:text-brand-600' => ! $isSelected,
                    ])>
                        <i class="ri-map-pin-2-line" aria-hidden="true"></i>
                    </span>
                </label>
            @endforeach
        </div>

        <a
            href="{{ route('customer.addresses.create') }}"
            class="inline-flex items-center gap-1.5 text-base font-medium text-brand-600 hover:text-brand-700"
        >
            <i class="ri-add-line" aria-hidden="true"></i>
            Add new address
        </a>

        <div class="mt-4">
            <label for="checkout_notes" class="block text-base font-medium text-gray-700">Delivery instructions (optional)</label>
            <textarea
                id="checkout_notes"
                name="notes"
                rows="3"
                maxlength="500"
                placeholder="e.g. Leave at the gate, call when you arrive"
                class="mt-1.5 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-base placeholder:text-gray-400 focus:border-brand-600 focus:ring-brand-600"
            >{{ old('notes', $checkout['notes'] ?? '') }}</textarea>
            <x-input-error for="notes" class="mt-1" />
        </div>

        <x-input-error for="address_id" class="mt-1" />

        <x-customer.checkout.actions :step="3" class="mt-2" />
    </form>
@endif
