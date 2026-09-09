<div class="flex flex-col">
    <div class="space-y-5 p-4 sm:p-5">
        <x-account-settings.panel
            icon="ri-device-line"
            title="Browser Sessions"
            description="Review devices where you are signed in and sign out remotely if needed."
        >
            <div class="flex items-start gap-3 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3">
                <i class="ri-information-line mt-0.5 shrink-0 text-lg text-blue-600" aria-hidden="true"></i>
                <p class="text-base text-blue-800">
                    If your account may have been compromised, log out of other sessions and update your password immediately.
                </p>
            </div>

            @if (count($this->sessions) > 0)
                <x-account-settings.card title="Active Sessions" description="Devices recently used to access your account.">
                    <div class="divide-y divide-gray-100 rounded-lg border border-gray-200">
                        @foreach ($this->sessions as $session)
                            <div class="flex items-start gap-3 px-4 py-3">
                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                                    @if ($session->agent->isDesktop())
                                        <i class="ri-computer-line text-xl" aria-hidden="true"></i>
                                    @else
                                        <i class="ri-smartphone-line text-xl" aria-hidden="true"></i>
                                    @endif
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="text-base font-medium text-gray-900">
                                        {{ $session->agent->platform() ?: __('Unknown') }}
                                        ·
                                        {{ $session->agent->browser() ?: __('Unknown') }}
                                    </p>
                                    <p class="mt-0.5 text-base text-gray-500">
                                        {{ $session->ip_address }}
                                        @if ($session->is_current_device)
                                            <span class="ml-1 inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-sm font-semibold text-green-800">
                                                <i class="ri-checkbox-circle-fill text-sm" aria-hidden="true"></i>
                                                This device
                                            </span>
                                        @else
                                            · {{ __('Last active') }} {{ $session->last_active }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-account-settings.card>
            @endif

            <x-account-settings.card>
                <p class="text-base font-semibold text-gray-900">Log out other sessions</p>
                <p class="mt-0.5 text-base text-gray-500">Sign out of all browsers and devices except this one.</p>
            </x-account-settings.card>
        </x-account-settings.panel>
    </div>

    <x-page-card.footer>
        <x-action-message class="text-base text-green-600 sm:mr-auto" on="loggedOut">
            {{ __('Done.') }}
        </x-action-message>

        <button
            type="button"
            wire:click="confirmLogout"
            wire:loading.attr="disabled"
            class="inline-flex items-center gap-1.5 rounded-lg bg-forest-600 px-4 py-2.5 text-base font-semibold text-white hover:bg-forest-700 disabled:opacity-50"
        >
            <i class="ri-logout-box-r-line" aria-hidden="true"></i>
            Log out others
        </button>
    </x-page-card.footer>

    <x-dialog-modal wire:model.live="confirmingLogout">
        <x-slot name="title">
            {{ __('Log Out Other Browser Sessions') }}
        </x-slot>

        <x-slot name="content">
            <p class="text-base text-gray-600">
                {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}
            </p>

            <div class="mt-4" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                <x-account-settings.field label="Password" for="logout-password" error="password">
                    <input
                        id="logout-password"
                        type="password"
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        x-ref="password"
                        wire:model="password"
                        wire:keydown.enter="logoutOtherBrowserSessions"
                        class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-base focus:border-brand-600 focus:ring-brand-600"
                    />
                </x-account-settings.field>
            </div>
        </x-slot>

        <x-slot name="footer">
            <button
                type="button"
                wire:click="$toggle('confirmingLogout')"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-base font-medium text-gray-700 hover:bg-gray-50"
            >
                Cancel
            </button>

            <button
                type="button"
                wire:click="logoutOtherBrowserSessions"
                wire:loading.attr="disabled"
                class="ms-3 inline-flex items-center gap-1.5 rounded-lg bg-forest-600 px-4 py-2.5 text-base font-semibold text-white hover:bg-forest-700 disabled:opacity-50"
            >
                Log out other sessions
            </button>
        </x-slot>
    </x-dialog-modal>
</div>
