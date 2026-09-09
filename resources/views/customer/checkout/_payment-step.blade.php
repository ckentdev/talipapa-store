@php
    use App\Enums\EWalletProvider;
    use App\Enums\PaymentMethod;

    $inputClass = $inputClass ?? 'block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-base placeholder:text-gray-400 transition focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/25';
    $labelClass = 'mb-1.5 block text-base font-medium text-gray-700';

    $selectedPayment = old('payment_method', $checkout['payment_method'] ?? PaymentMethod::Cod->value);
    if ($selectedPayment === 'gcash') {
        $selectedPayment = PaymentMethod::EWallet->value;
    }

    $selectedEwallet = old('ewallet_provider', $checkout['ewallet_provider'] ?? EWalletProvider::Gcash->value);

    $methodIcons = [
        PaymentMethod::Cod->value => 'ri-money-dollar-circle-line',
        PaymentMethod::EWallet->value => 'ri-smartphone-line',
        PaymentMethod::Card->value => 'ri-bank-card-line',
    ];

    $methodAccents = [
        PaymentMethod::Cod->value => 'text-amber-700 bg-amber-100',
        PaymentMethod::EWallet->value => 'text-blue-700 bg-blue-100',
        PaymentMethod::Card->value => 'text-purple-700 bg-purple-100',
    ];
@endphp

<form
    id="checkout-payment-form"
    method="POST"
    action="{{ route('customer.checkout.process') }}"
    class="space-y-4"
>
    @csrf
    <input type="hidden" name="step" value="4">

    <div class="space-y-3">
        @foreach (PaymentMethod::forOnlineCheckout() as $method)
            <div
                data-checkout-payment-option="{{ $method->value }}"
                @class([
                    'checkout-payment-option rounded-xl border-2 transition-all duration-200',
                    'border-brand-600 bg-brand-50/30 shadow-sm ring-2 ring-brand-600/15' => $selectedPayment === $method->value,
                    'border-gray-200 bg-white hover:border-gray-300' => $selectedPayment !== $method->value,
                ])
            >
                <label class="flex cursor-pointer items-start gap-4 p-4 transition-colors has-[:focus-visible]:bg-brand-50/20">
                    <input
                        type="radio"
                        name="payment_method"
                        value="{{ $method->value }}"
                        @checked($selectedPayment === $method->value)
                        class="payment-method-radio sr-only"
                        data-payment-panel="{{ $method->value }}"
                    >
                    {{-- <span @class([
                        'flex shrink-0 items-center justify-center rounded-xl',
                        $method === PaymentMethod::EWallet ? 'h-auto w-auto bg-transparent p-0' : 'h-11 w-11',
                        $methodAccents[$method->value] ?? 'bg-gray-100 text-gray-600',
                    ])>
                        @if ($method === PaymentMethod::EWallet)
                            <x-payment.ewallet-logo-strip />
                        @else
                            <i class="{{ $methodIcons[$method->value] ?? 'ri-wallet-3-line' }} text-xl" aria-hidden="true"></i>
                        @endif
                    </span> --}}
                    <div class="min-w-0 flex-1 pt-0.5">
                        <span class="font-semibold text-gray-900">{{ $method->label() }}</span>
                        <p class="mt-0.5 text-base text-gray-500">{{ $method->description() }}</p>
                    </div>
                    <span
                        @class([
                            'mt-1 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition',
                            'border-brand-600 bg-brand-600 text-white' => $selectedPayment === $method->value,
                            'border-gray-300 bg-white text-transparent' => $selectedPayment !== $method->value,
                        ])
                        aria-hidden="true"
                    >
                        <i class="ri-check-line text-xs"></i>
                    </span>
                </label>

                @if ($method === PaymentMethod::EWallet)
                    <div
                        id="ewallet-panel"
                        class="border-t border-blue-100/80 bg-gradient-to-b from-blue-50/50 to-white px-4 pb-4 pt-3 {{ $selectedPayment === PaymentMethod::EWallet->value ? '' : 'hidden' }}"
                    >
                        <p class="text-base font-medium text-gray-900">Select e-Wallet</p>
                        <p class="mt-0.5 text-base text-gray-500">Choose the app you want to pay with.</p>

                        <x-payment.ewallet-providers
                            :selected="$selectedEwallet"
                            class="mt-3"
                        />

                        <x-input-error for="ewallet_provider" class="mt-2" />
                    </div>
                @elseif ($method === PaymentMethod::Card)
                    <div
                        id="card-panel"
                        class="border-t border-gray-200/80 bg-white/70 px-4 pb-4 pt-3 {{ $selectedPayment === PaymentMethod::Card->value ? '' : 'hidden' }}"
                    >
                        <p class="text-base font-medium text-gray-700">Card Details</p>
                        <p class="mt-0.5 text-base text-gray-500">Enter your credit or debit card information.</p>

                        <div class="mt-3 space-y-4">
                            <div>
                                <label for="card_holder_name" class="{{ $labelClass }}">Name on card</label>
                                <input
                                    id="card_holder_name"
                                    type="text"
                                    name="card_holder_name"
                                    value="{{ old('card_holder_name', $checkout['payment']['holder'] ?? '') }}"
                                    autocomplete="cc-name"
                                    placeholder="As shown on card"
                                    class="{{ $inputClass }} card-field"
                                >
                                <x-input-error for="card_holder_name" class="mt-1" />
                            </div>

                            <div>
                                <label for="card_number" class="{{ $labelClass }}">Card number</label>
                                <input
                                    id="card_number"
                                    type="text"
                                    name="card_number"
                                    value="{{ old('card_number') }}"
                                    inputmode="numeric"
                                    autocomplete="cc-number"
                                    placeholder="1234 5678 9012 3456"
                                    maxlength="19"
                                    class="{{ $inputClass }} card-field"
                                >
                                <x-input-error for="card_number" class="mt-1" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="card_expiry" class="{{ $labelClass }}">Expiry (MM/YY)</label>
                                    <input
                                        id="card_expiry"
                                        type="text"
                                        name="card_expiry"
                                        value="{{ old('card_expiry', $checkout['payment']['expiry'] ?? '') }}"
                                        inputmode="numeric"
                                        autocomplete="cc-exp"
                                        placeholder="MM/YY"
                                        maxlength="5"
                                        class="{{ $inputClass }} card-field"
                                    >
                                    <x-input-error for="card_expiry" class="mt-1" />
                                </div>
                                <div>
                                    <label for="card_cvv" class="{{ $labelClass }}">CVV</label>
                                    <input
                                        id="card_cvv"
                                        type="password"
                                        name="card_cvv"
                                        value="{{ old('card_cvv') }}"
                                        inputmode="numeric"
                                        autocomplete="cc-csc"
                                        placeholder="123"
                                        maxlength="4"
                                        class="{{ $inputClass }} card-field"
                                    >
                                    <x-input-error for="card_cvv" class="mt-1" />
                                </div>
                            </div>
                        </div>

                        <p class="mt-3 flex items-start gap-2 text-base text-gray-500">
                            <i class="ri-lock-line mt-0.5 shrink-0" aria-hidden="true"></i>
                            Card details are used for this checkout only. Payment gateway integration is not enabled yet.
                        </p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <x-customer.checkout.actions :step="4" />
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('checkout-payment-form');
        if (! form) return;

        const ewalletPanel = document.getElementById('ewallet-panel');
        const cardPanel = document.getElementById('card-panel');
        const methodRadios = form.querySelectorAll('.payment-method-radio');
        const ewalletFields = form.querySelectorAll('.ewallet-field');
        const cardFields = form.querySelectorAll('.card-field');
        const paymentOptions = form.querySelectorAll('[data-checkout-payment-option]');

        const syncPanels = () => {
            const selected = form.querySelector('.payment-method-radio:checked')?.value;

            ewalletPanel?.classList.toggle('hidden', selected !== 'ewallet');
            cardPanel?.classList.toggle('hidden', selected !== 'card');

            paymentOptions.forEach((option) => {
                const radio = option.querySelector('.payment-method-radio');
                if (! radio) return;

                const isSelected = radio.value === selected;
                option.classList.toggle('border-brand-600', isSelected);
                option.classList.toggle('bg-brand-50/30', isSelected);
                option.classList.toggle('shadow-sm', isSelected);
                option.classList.toggle('ring-2', isSelected);
                option.classList.toggle('ring-brand-600/15', isSelected);
                option.classList.toggle('border-gray-200', ! isSelected);
                option.classList.toggle('bg-white', ! isSelected);
                option.classList.toggle('hover:border-gray-300', ! isSelected);
            });

            methodRadios.forEach((radio) => {
                const checkmark = radio.closest('label')?.querySelector('.rounded-full.border-2');
                if (! checkmark) return;

                const isSelected = radio.value === selected;
                checkmark.classList.toggle('border-brand-600', isSelected);
                checkmark.classList.toggle('bg-brand-600', isSelected);
                checkmark.classList.toggle('text-white', isSelected);
                checkmark.classList.toggle('border-gray-300', ! isSelected);
                checkmark.classList.toggle('bg-white', ! isSelected);
                checkmark.classList.toggle('text-transparent', ! isSelected);
            });

            ewalletFields.forEach((field) => {
                field.required = selected === 'ewallet';
                field.disabled = selected !== 'ewallet';
            });

            cardFields.forEach((field) => {
                field.required = selected === 'card';
                field.disabled = selected !== 'card';
            });
        };

        methodRadios.forEach((radio) => radio.addEventListener('change', syncPanels));
        syncPanels();

        const cardNumber = document.getElementById('card_number');
        cardNumber?.addEventListener('input', () => {
            const digits = cardNumber.value.replace(/\D/g, '').slice(0, 19);
            cardNumber.value = digits.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
        });

        const cardExpiry = document.getElementById('card_expiry');
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
