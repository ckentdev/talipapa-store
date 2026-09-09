<form method="POST" action="{{ route('customer.checkout.process') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="step" value="2">

    <div class="grid gap-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="checkout_name" class="mb-1.5 block text-base font-medium text-gray-700">Full Name</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3.5 text-gray-400">
                    <i class="ri-user-3-line" aria-hidden="true"></i>
                </span>
                <input
                    id="checkout_name"
                    type="text"
                    name="name"
                    value="{{ old('name', $checkout['customer']['name'] ?? auth()->user()->name) }}"
                    required
                    autocomplete="name"
                    placeholder="Your full name"
                    class="{{ $inputClass }} ps-10"
                >
            </div>
            <x-input-error for="name" class="mt-1" />
        </div>

        <div class="sm:col-span-2">
            <label for="checkout_phone" class="mb-1.5 block text-base font-medium text-gray-700">Phone Number</label>
            <div class="flex overflow-hidden rounded-lg border border-gray-300 focus-within:border-brand-600 focus-within:ring-1 focus-within:ring-brand-600">
                <span class="inline-flex shrink-0 items-center border-r border-gray-300 bg-gray-50 px-3.5 text-base font-medium text-gray-600">
                    {{ \App\Support\PhilippinePhone::PREFIX }}
                </span>
                <input
                    id="checkout_phone"
                    type="tel"
                    name="phone"
                    value="{{ old('phone', $checkout['customer']['phone'] ?? auth()->user()->phone) }}"
                    required
                    autocomplete="tel"
                    placeholder="9XX XXX XXXX"
                    class="block w-full min-w-0 border-0 px-3 py-2.5 text-base placeholder:text-gray-400 focus:ring-0"
                >
            </div>
            <x-input-error for="phone" class="mt-1" />
            <p class="mt-1.5 text-base text-gray-500">We'll use this to contact you about your delivery.</p>
        </div>
    </div>

    <x-customer.checkout.actions :step="2" class="mt-2" />
</form>
