<div class="flex flex-col">
    <div class="space-y-5 p-4 sm:p-5">
        <x-account-settings.panel
            icon="ri-delete-bin-line"
            title="Delete Account"
            description="Permanently remove your account and all associated data."
        >
            <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600">
                        <i class="ri-error-warning-line text-xl" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h4 class="text-base font-bold text-red-900">Danger zone</h4>
                        <p class="mt-1 text-base text-red-800">
                            Once your account is deleted, all of its resources and data will be permanently removed. This action cannot be undone.
                        </p>
                        <p class="mt-2 text-base text-red-700">
                            Download any data you need before proceeding. Your store, products, and order history will also be affected.
                        </p>
                    </div>
                </div>
            </div>

            <x-account-settings.card>
                <p class="text-base font-semibold text-gray-900">Delete your account</p>
                <p class="mt-0.5 text-base text-gray-500">You will be asked to confirm with your password.</p>
            </x-account-settings.card>
        </x-account-settings.panel>
    </div>

    <x-page-card.footer>
        <button
            type="button"
            wire:click="confirmUserDeletion"
            wire:loading.attr="disabled"
            class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-4 py-2.5 text-base font-semibold text-white hover:bg-red-700 disabled:opacity-50"
        >
            <i class="ri-delete-bin-line" aria-hidden="true"></i>
            Delete account
        </button>
    </x-page-card.footer>

    <x-dialog-modal wire:model.live="confirmingUserDeletion">
        <x-slot name="title">
            {{ __('Delete Account') }}
        </x-slot>

        <x-slot name="content">
            <p class="text-base text-gray-600">
                {{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                <x-account-settings.field label="Password" for="delete-password" error="password">
                    <input
                        id="delete-password"
                        type="password"
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        x-ref="password"
                        wire:model="password"
                        wire:keydown.enter="deleteUser"
                        class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-base focus:border-brand-600 focus:ring-brand-600"
                    />
                </x-account-settings.field>
            </div>
        </x-slot>

        <x-slot name="footer">
            <button
                type="button"
                wire:click="$toggle('confirmingUserDeletion')"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-base font-medium text-gray-700 hover:bg-gray-50"
            >
                Cancel
            </button>

            <button
                type="button"
                wire:click="deleteUser"
                wire:loading.attr="disabled"
                class="ms-3 inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-4 py-2.5 text-base font-semibold text-white hover:bg-red-700 disabled:opacity-50"
            >
                Delete account
            </button>
        </x-slot>
    </x-dialog-modal>
</div>
