@props([
    'selected' => null,
    'disabled' => false,
    'inputClass' => 'ewallet-field',
    'compact' => false,
])

@php
    use App\Enums\EWalletProvider;

    $selectedProvider = $selected ?? old('ewallet_provider', EWalletProvider::Gcash->value);
@endphp

<div
    {{ $attributes->merge([
        'class' => $compact
            ? 'grid grid-cols-2 gap-2'
            : 'grid grid-cols-2 gap-3 sm:grid-cols-3',
    ]) }}
    role="radiogroup"
    aria-label="Select e-Wallet provider"
>
    @foreach (EWalletProvider::cases() as $provider)
        <label
            @class([
                'group relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 p-2 text-center shadow-sm transition-all duration-150 has-[:checked]:shadow-md sm:p-2.5',
                'pointer-events-none opacity-60' => $disabled,
                match ($provider) {
                    EWalletProvider::Gcash => 'border-blue-100 bg-blue-50/40 hover:border-blue-300 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:ring-2 has-[:checked]:ring-blue-200',
                    EWalletProvider::Maya => 'border-green-100 bg-green-50/40 hover:border-green-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50 has-[:checked]:ring-2 has-[:checked]:ring-green-200',
                    EWalletProvider::GrabPay => 'border-emerald-100 bg-emerald-50/40 hover:border-emerald-300 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 has-[:checked]:ring-2 has-[:checked]:ring-emerald-200',
                    EWalletProvider::ShopeePay => 'border-orange-100 bg-orange-50/40 hover:border-orange-300 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50 has-[:checked]:ring-2 has-[:checked]:ring-orange-200',
                    EWalletProvider::Coins => 'border-yellow-100 bg-yellow-50/40 hover:border-yellow-300 has-[:checked]:border-yellow-500 has-[:checked]:bg-yellow-50 has-[:checked]:ring-2 has-[:checked]:ring-yellow-200',
                },
            ])
        >
            <input
                type="radio"
                name="ewallet_provider"
                value="{{ $provider->value }}"
                @checked($selectedProvider === $provider->value)
                @disabled($disabled)
                class="{{ $inputClass }} sr-only"
            >

            <span @class([
                'mx-auto flex w-full items-center justify-center overflow-hidden rounded-lg bg-white px-2 py-1.5 ring-1 ring-black/5',
                $compact ? 'h-11 sm:h-12' : 'h-12 sm:h-14',
            ])>
                <img
                    src="{{ $provider->logoUrl() }}"
                    alt="{{ $provider->label() }}"
                    class="h-full w-auto max-w-full object-contain"
                    loading="lazy"
                >
            </span>

            <span
                class="absolute right-1.5 top-1.5 inline-flex h-4 w-4 items-center justify-center rounded-full border-2 border-gray-200 bg-white text-transparent opacity-0 transition group-has-[:checked]:border-brand-600 group-has-[:checked]:bg-brand-600 group-has-[:checked]:text-white group-has-[:checked]:opacity-100 sm:right-2 sm:top-2 sm:h-5 sm:w-5"
                aria-hidden="true"
            >
                <i class="ri-check-line text-[10px] sm:text-xs"></i>
            </span>
        </label>
    @endforeach
</div>
