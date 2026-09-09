@php
    $inputClass = 'block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-base placeholder:text-gray-400 focus:border-brand-600 focus:ring-brand-600';
@endphp

<div class="flex flex-col">
    <div class="space-y-5 p-4 sm:p-5">
        <x-account-settings.panel
            icon="ri-lock-password-line"
            title="Update Password"
            description="Use a strong, unique password to keep your store account secure."
        >
            <div class="flex items-start gap-3 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3">
                <i class="ri-information-line mt-0.5 shrink-0 text-lg text-blue-600" aria-hidden="true"></i>
                <p class="text-base text-blue-800">
                    Choose a password with at least 8 characters. Mix letters, numbers, and symbols for better security.
                </p>
            </div>

            <x-account-settings.form submit="updatePassword" form-id="store-password-form">
                <x-account-settings.card title="Change Password">
                    <div class="grid grid-cols-1 gap-4">
                        <x-account-settings.field label="Current password" for="current_password" error="current_password">
                            <input
                                id="current_password"
                                type="password"
                                wire:model="state.current_password"
                                autocomplete="current-password"
                                class="{{ $inputClass }}"
                            />
                        </x-account-settings.field>

                        <x-account-settings.field label="New password" for="password" error="password">
                            <input
                                id="password"
                                type="password"
                                wire:model="state.password"
                                autocomplete="new-password"
                                class="{{ $inputClass }}"
                            />
                        </x-account-settings.field>

                        <x-account-settings.field label="Confirm new password" for="password_confirmation" error="password_confirmation">
                            <input
                                id="password_confirmation"
                                type="password"
                                wire:model="state.password_confirmation"
                                autocomplete="new-password"
                                class="{{ $inputClass }}"
                            />
                        </x-account-settings.field>
                    </div>
                </x-account-settings.card>
            </x-account-settings.form>
        </x-account-settings.panel>
    </div>

    <x-page-card.footer>
        <x-action-message class="text-base text-green-600 sm:mr-auto" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <a href="{{ route('store.dashboard') }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50">
            <i class="ri-close-line" aria-hidden="true"></i>
            Cancel
        </a>

        <button
            type="submit"
            form="store-password-form"
            class="inline-flex items-center gap-1.5 rounded-lg bg-forest-600 px-4 py-2.5 text-base font-semibold text-white hover:bg-forest-700 disabled:opacity-50"
        >
            <i class="ri-lock-line" aria-hidden="true"></i>
            Update password
        </button>
    </x-page-card.footer>
</div>
