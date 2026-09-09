@props([
    'disabled' => false,
])

@php
    use App\Enums\EWalletProvider;
    use App\Enums\PaymentMethod;

    $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm placeholder:text-gray-400 transition focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/25';
    $labelClass = 'mb-1 block text-sm font-medium text-gray-700';

    $selectedPayment = old('payment_method', PaymentMethod::Cash->value);
    if ($selectedPayment === 'gcash') {
        $selectedPayment = PaymentMethod::EWallet->value;
    }
    if ($selectedPayment === PaymentMethod::Cod->value) {
        $selectedPayment = PaymentMethod::Cash->value;
    }

    $selectedEwallet = old('ewallet_provider', EWalletProvider::Gcash->value);

    $methodIcons = [
        PaymentMethod::Cash->value => 'ri-hand-coin-line',
        PaymentMethod::EWallet->value => 'ri-smartphone-line',
        PaymentMethod::Card->value => 'ri-bank-card-line',
    ];

    $methodAccents = [
        PaymentMethod::Cash->value => 'text-green-700 bg-green-100',
        PaymentMethod::EWallet->value => 'text-blue-700 bg-blue-100',
        PaymentMethod::Card->value => 'text-purple-700 bg-purple-100',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Payment method</span>

    <div class="space-y-2.5">
        @foreach (PaymentMethod::forPos() as $method)
            <div
                data-pos-payment-option="{{ $method->value }}"
                @class([
                    'pos-payment-option rounded-xl border-2 transition-all duration-200',
                    'border-forest-600 bg-forest-50/40 shadow-sm ring-2 ring-forest-600/15' => $selectedPayment === $method->value,
                    'border-gray-200 bg-white hover:border-gray-300' => $selectedPayment !== $method->value,
                ])
            >
                <label @class([
                    'flex cursor-pointer items-start gap-3 p-3 transition-colors has-[:focus-visible]:bg-forest-50/30',
                    'pointer-events-none opacity-60' => $disabled,
                ])>
                    <input
                        type="radio"
                        name="payment_method"
                        value="{{ $method->value }}"
                        @checked($selectedPayment === $method->value)
                        @disabled($disabled)
                        class="pos-payment-method-radio sr-only"
                        data-payment-panel="{{ $method->value }}"
                    >
                    {{-- <span @class([
                        'flex shrink-0 items-center justify-center rounded-xl',
                        $method === PaymentMethod::EWallet ? 'h-auto w-auto bg-transparent p-0' : 'h-10 w-10',
                        $methodAccents[$method->value] ?? 'bg-gray-100 text-gray-600',
                    ])>
                        @if ($method === PaymentMethod::EWallet)
                            <x-payment.ewallet-logo-strip size="sm" />
                        @else
                            <i class="{{ $methodIcons[$method->value] ?? 'ri-wallet-3-line' }} text-lg" aria-hidden="true"></i>
                        @endif
                    </span> --}}
                    <div class="min-w-0 flex-1 pt-0.5">
                        <span class="text-sm font-semibold text-gray-900">{{ $method->label() }}</span>
                        <p class="mt-0.5 text-xs leading-relaxed text-gray-500">{{ $method->description() }}</p>
                    </div>
                    <span
                        @class([
                            'mt-1 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition',
                            'border-forest-600 bg-forest-600 text-white' => $selectedPayment === $method->value,
                            'border-gray-300 bg-white text-transparent' => $selectedPayment !== $method->value,
                        ])
                        aria-hidden="true"
                    >
                        <i class="ri-check-line text-xs"></i>
                    </span>
                </label>

                @if ($method === PaymentMethod::Cash)
                    <div
                        id="pos-cash-panel"
                        class="border-t border-green-200/80 bg-green-50/40 px-3 pb-3 pt-2.5 {{ $selectedPayment === PaymentMethod::Cash->value ? '' : 'hidden' }}"
                    >
                        <p class="text-sm font-medium text-green-900">Cash received</p>
                        <p class="mt-0.5 text-xs text-green-800/80">Enter the amount the walk-in customer paid.</p>

                        <div class="relative mt-2.5">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-green-700">₱</span>
                            <input
                                id="cash_tendered"
                                type="number"
                                x-model="cashTendered"
                                name="cash_tendered"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                value="{{ old('cash_tendered') }}"
                                @disabled($disabled)
                                class="block w-full rounded-lg border border-green-200 bg-white py-2.5 pl-8 pr-3 text-sm font-semibold transition focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-600/25"
                            >
                        </div>
                        @error('cash_tendered')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <div
                            x-show="cashTenderedAmount >= totalDue && totalDue > 0"
                            x-cloak
                            class="mt-2 flex items-center justify-between rounded-lg border border-green-200 bg-white px-3 py-2 text-sm"
                        >
                            <span class="font-medium text-gray-700">Change</span>
                            <span class="font-bold text-green-700" x-text="formatMoney(changeDue)"></span>
                        </div>
                        <div
                            x-show="cashTenderedAmount > 0 && cashTenderedAmount < totalDue"
                            x-cloak
                            class="mt-2 text-xs font-medium text-red-600"
                        >
                            Need <span x-text="formatMoney(totalDue - cashTenderedAmount)"></span> more
                        </div>
                    </div>
                @elseif ($method === PaymentMethod::EWallet)
                    <div
                        id="pos-ewallet-panel"
                        class="border-t border-blue-100/80 bg-gradient-to-b from-blue-50/50 to-white px-3 pb-3 pt-2.5 {{ $selectedPayment === PaymentMethod::EWallet->value ? '' : 'hidden' }}"
                    >
                        <p class="text-sm font-medium text-gray-900">Select e-Wallet</p>
                        <p class="mt-0.5 text-xs text-gray-500">Choose the app the customer paid with.</p>

                        <x-payment.ewallet-providers
                            :selected="$selectedEwallet"
                            :disabled="$disabled"
                            input-class="pos-ewallet-field"
                            compact
                            class="mt-2.5"
                        />

                        @error('ewallet_provider')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @elseif ($method === PaymentMethod::Card)
                    <div
                        id="pos-card-panel"
                        class="border-t border-gray-200/80 bg-white/70 px-3 pb-3 pt-2.5 {{ $selectedPayment === PaymentMethod::Card->value ? '' : 'hidden' }}"
                    >
                        <p class="text-sm font-medium text-gray-700">Card details</p>
                        <p class="mt-0.5 text-xs text-gray-500">Enter the customer's card information.</p>

                        <div class="mt-2.5 space-y-3">
                            <div>
                                <label for="pos_card_holder_name" class="{{ $labelClass }}">Name on card</label>
                                <input
                                    id="pos_card_holder_name"
                                    type="text"
                                    name="card_holder_name"
                                    value="{{ old('card_holder_name') }}"
                                    autocomplete="cc-name"
                                    placeholder="As shown on card"
                                    @disabled($disabled)
                                    class="{{ $inputClass }} pos-card-field"
                                >
                                @error('card_holder_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="pos_card_number" class="{{ $labelClass }}">Card number</label>
                                <input
                                    id="pos_card_number"
                                    type="text"
                                    name="card_number"
                                    value="{{ old('card_number') }}"
                                    inputmode="numeric"
                                    autocomplete="cc-number"
                                    placeholder="1234 5678 9012 3456"
                                    maxlength="19"
                                    @disabled($disabled)
                                    class="{{ $inputClass }} pos-card-field"
                                >
                                @error('card_number')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="pos_card_expiry" class="{{ $labelClass }}">Expiry (MM/YY)</label>
                                    <input
                                        id="pos_card_expiry"
                                        type="text"
                                        name="card_expiry"
                                        value="{{ old('card_expiry') }}"
                                        inputmode="numeric"
                                        autocomplete="cc-exp"
                                        placeholder="MM/YY"
                                        maxlength="5"
                                        @disabled($disabled)
                                        class="{{ $inputClass }} pos-card-field"
                                    >
                                    @error('card_expiry')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="pos_card_cvv" class="{{ $labelClass }}">CVV</label>
                                    <input
                                        id="pos_card_cvv"
                                        type="password"
                                        name="card_cvv"
                                        value="{{ old('card_cvv') }}"
                                        inputmode="numeric"
                                        autocomplete="cc-csc"
                                        placeholder="123"
                                        maxlength="4"
                                        @disabled($disabled)
                                        class="{{ $inputClass }} pos-card-field"
                                    >
                                    @error('card_cvv')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <p class="mt-2.5 flex items-start gap-1.5 text-xs text-gray-500">
                            <i class="ri-lock-line mt-0.5 shrink-0" aria-hidden="true"></i>
                            Card details are recorded for this sale only. Payment gateway integration is not enabled yet.
                        </p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @error('payment_method')
        <p class="text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('pos-checkout-form');
                if (! form) return;

                const cashPanel = document.getElementById('pos-cash-panel');
                const ewalletPanel = document.getElementById('pos-ewallet-panel');
                const cardPanel = document.getElementById('pos-card-panel');
                const methodRadios = form.querySelectorAll('.pos-payment-method-radio');
                const ewalletFields = form.querySelectorAll('.pos-ewallet-field');
                const cardFields = form.querySelectorAll('.pos-card-field');
                const paymentOptions = form.querySelectorAll('[data-pos-payment-option]');

                const syncPanels = () => {
                    const selected = form.querySelector('.pos-payment-method-radio:checked')?.value;

                    cashPanel?.classList.toggle('hidden', selected !== 'cash');
                    ewalletPanel?.classList.toggle('hidden', selected !== 'ewallet');
                    cardPanel?.classList.toggle('hidden', selected !== 'card');

                    paymentOptions.forEach((option) => {
                        const radio = option.querySelector('.pos-payment-method-radio');
                        if (! radio) return;

                        const isSelected = radio.value === selected;
                        option.classList.toggle('border-forest-600', isSelected);
                        option.classList.toggle('bg-forest-50/40', isSelected);
                        option.classList.toggle('shadow-sm', isSelected);
                        option.classList.toggle('ring-2', isSelected);
                        option.classList.toggle('ring-forest-600/15', isSelected);
                        option.classList.toggle('border-gray-200', ! isSelected);
                        option.classList.toggle('bg-white', ! isSelected);
                        option.classList.toggle('hover:border-gray-300', ! isSelected);
                    });

                    methodRadios.forEach((radio) => {
                        const checkmark = radio.closest('label')?.querySelector('.rounded-full.border-2');
                        if (! checkmark) return;

                        const isSelected = radio.value === selected;
                        checkmark.classList.toggle('border-forest-600', isSelected);
                        checkmark.classList.toggle('bg-forest-600', isSelected);
                        checkmark.classList.toggle('text-white', isSelected);
                        checkmark.classList.toggle('border-gray-300', ! isSelected);
                        checkmark.classList.toggle('bg-white', ! isSelected);
                        checkmark.classList.toggle('text-transparent', ! isSelected);
                    });

                    ewalletFields.forEach((field) => {
                        field.required = selected === 'ewallet' && ! field.disabled;
                    });

                    cardFields.forEach((field) => {
                        field.required = selected === 'card' && ! field.disabled;
                    });

                    form.dispatchEvent(new CustomEvent('pos-payment-changed', {
                        bubbles: true,
                        detail: { method: selected ?? 'cash' },
                    }));
                };

                methodRadios.forEach((radio) => radio.addEventListener('change', syncPanels));
                syncPanels();

                const cardNumber = document.getElementById('pos_card_number');
                cardNumber?.addEventListener('input', () => {
                    const digits = cardNumber.value.replace(/\D/g, '').slice(0, 19);
                    cardNumber.value = digits.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
                });

                const cardExpiry = document.getElementById('pos_card_expiry');
                cardExpiry?.addEventListener('input', () => {
                    let value = cardExpiry.value.replace(/\D/g, '').slice(0, 4);
                    if (value.length >= 3) {
                        value = `${value.slice(0, 2)}/${value.slice(2)}`;
                    }
                    cardExpiry.value = value;
                });
            });
        </script>
    @endpush
@endonce
