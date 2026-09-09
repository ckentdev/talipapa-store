<div class="flex flex-col">
    <div class="space-y-5 p-4 sm:p-5">
        <x-account-settings.panel
            icon="ri-shield-keyhole-line"
            title="Two-Factor Authentication"
            description="Add an extra layer of security when signing in to your account."
        >
            <x-account-settings.card>
                <div class="flex flex-wrap items-center gap-3">
                    <span @class([
                        'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-base font-semibold',
                        'bg-green-100 text-green-800' => $this->enabled && ! $showingConfirmation,
                        'bg-yellow-100 text-yellow-800' => $this->enabled && $showingConfirmation,
                        'bg-gray-100 text-gray-700' => ! $this->enabled,
                    ])>
                        <i @class([
                            'ri-shield-check-line' => $this->enabled && ! $showingConfirmation,
                            'ri-loader-4-line' => $this->enabled && $showingConfirmation,
                            'ri-shield-line' => ! $this->enabled,
                        ]) aria-hidden="true"></i>
                        @if ($this->enabled)
                            @if ($showingConfirmation)
                                Setup in progress
                            @else
                                Enabled
                            @endif
                        @else
                            Not enabled
                        @endif
                    </span>
                </div>

                <p class="mt-4 text-base text-gray-600">
                    When enabled, you will be asked for a secure code from your authenticator app each time you sign in.
                </p>

                @if ($this->enabled)
                    @if ($showingQrCode)
                        <div class="mt-5 rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="text-base font-semibold text-gray-900">
                                @if ($showingConfirmation)
                                    Scan this QR code with your authenticator app, then enter the verification code below.
                                @else
                                    Scan this QR code with your authenticator app to set up two-factor authentication.
                                @endif
                            </p>

                            <div class="mt-4 inline-block rounded-lg border border-gray-200 bg-white p-3">
                                {!! $this->user->twoFactorQrCodeSvg() !!}
                            </div>

                            <div class="mt-4 rounded-lg border border-gray-200 bg-white px-4 py-3">
                                <p class="text-base font-medium text-gray-700">Setup key</p>
                                <p class="mt-1 break-all font-mono text-base text-gray-900">{{ decrypt($this->user->two_factor_secret) }}</p>
                            </div>

                            @if ($showingConfirmation)
                                <div class="mt-4 max-w-sm">
                                    <x-account-settings.field label="Verification code" for="code" error="code">
                                        <input
                                            id="code"
                                            type="text"
                                            wire:model="code"
                                            wire:keydown.enter="confirmTwoFactorAuthentication"
                                            inputmode="numeric"
                                            autofocus
                                            autocomplete="one-time-code"
                                            class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-base focus:border-brand-600 focus:ring-brand-600"
                                        />
                                    </x-account-settings.field>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($showingRecoveryCodes)
                        <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4">
                            <p class="flex items-start gap-2 text-base font-semibold text-amber-900">
                                <i class="ri-alert-line mt-0.5 shrink-0" aria-hidden="true"></i>
                                Store these recovery codes in a safe place. Each code can only be used once.
                            </p>

                            <div class="mt-4 grid gap-1 rounded-lg border border-amber-200 bg-white px-4 py-4 font-mono text-base text-gray-800">
                                @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                                    <div>{{ $code }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="mt-5 flex items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                        <i class="ri-smartphone-line mt-0.5 shrink-0 text-lg text-gray-500" aria-hidden="true"></i>
                        <p class="text-base text-gray-600">
                            Use Google Authenticator, Authy, or any compatible app to generate login codes.
                        </p>
                    </div>
                @endif
            </x-account-settings.card>
        </x-account-settings.panel>
    </div>

    <x-page-card.footer>
        @if (! $this->enabled)
            <x-confirms-password wire:then="enableTwoFactorAuthentication">
                <button
                    type="button"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-forest-600 px-4 py-2.5 text-base font-semibold text-white hover:bg-forest-700 disabled:opacity-50"
                >
                    <i class="ri-shield-check-line" aria-hidden="true"></i>
                    Enable two-factor
                </button>
            </x-confirms-password>
        @else
            @if ($showingRecoveryCodes)
                <x-confirms-password wire:then="regenerateRecoveryCodes">
                    <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-base font-medium text-gray-700 hover:bg-gray-50">
                        <i class="ri-refresh-line" aria-hidden="true"></i>
                        Regenerate codes
                    </button>
                </x-confirms-password>
            @elseif ($showingConfirmation)
                <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                    <button
                        type="button"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-forest-600 px-4 py-2.5 text-base font-semibold text-white hover:bg-forest-700 disabled:opacity-50"
                    >
                        <i class="ri-check-line" aria-hidden="true"></i>
                        Confirm setup
                    </button>
                </x-confirms-password>
            @else
                <x-confirms-password wire:then="showRecoveryCodes">
                    <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-base font-medium text-gray-700 hover:bg-gray-50">
                        <i class="ri-key-2-line" aria-hidden="true"></i>
                        Show recovery codes
                    </button>
                </x-confirms-password>
            @endif

            @if ($showingConfirmation)
                <x-confirms-password wire:then="disableTwoFactorAuthentication">
                    <button
                        type="button"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-base font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                </x-confirms-password>
            @else
                <x-confirms-password wire:then="disableTwoFactorAuthentication">
                    <button
                        type="button"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-base font-semibold text-red-700 hover:bg-red-100 disabled:opacity-50"
                    >
                        <i class="ri-shield-cross-line" aria-hidden="true"></i>
                        Disable
                    </button>
                </x-confirms-password>
            @endif
        @endif
    </x-page-card.footer>
</div>
