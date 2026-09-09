@php
    $inputClass = 'block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-base placeholder:text-gray-400 focus:border-brand-600 focus:ring-brand-600';
@endphp

<div class="flex flex-col">
    <div class="space-y-5 p-4 sm:p-5">
        <x-account-settings.panel
            icon="ri-user-3-line"
            title="Profile Information"
            description="Update your profile photo, name, email, and phone number."
        >
            <div class="flex flex-col items-start gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4 sm:flex-row sm:items-center">
                <img
                    src="{{ $this->user->profile_photo_url }}"
                    alt="{{ $this->user->name }}"
                    class="h-20 w-20 shrink-0 rounded-full object-cover ring-4 ring-white shadow-md"
                >
                <div class="min-w-0">
                    <p class="truncate text-base font-semibold text-gray-900">{{ $this->user->name }}</p>
                    <p class="truncate text-base text-gray-500">{{ $this->user->email }}</p>
                    @if ($this->user->phone)
                        <p class="mt-1 flex items-center gap-1.5 text-base text-gray-500">
                            <i class="ri-phone-line text-brand-600" aria-hidden="true"></i>
                            {{ $this->user->phone }}
                        </p>
                    @endif
                </div>
            </div>

            <x-account-settings.form submit="updateProfileInformation" form-id="store-profile-form">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <x-account-settings.card title="Profile Photo" description="Choose a clear photo customers and admins can recognize.">
                        <div x-data="{photoName: null, photoPreview: null}">
                            <input
                                type="file"
                                id="photo"
                                class="hidden"
                                wire:model.live="photo"
                                x-ref="photo"
                                x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => { photoPreview = e.target.result; };
                                    reader.readAsDataURL($refs.photo.files[0]);
                                "
                            />

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                <div class="shrink-0" x-show="photoPreview" style="display: none;">
                                    <span
                                        class="block h-20 w-20 rounded-full bg-cover bg-center bg-no-repeat ring-4 ring-white shadow-md"
                                        x-bind:style="'background-image: url(\'' + photoPreview + '\');'"
                                    ></span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-base text-gray-500">JPG, PNG, or GIF up to 1MB.</p>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <button
                                            type="button"
                                            x-on:click.prevent="$refs.photo.click()"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50"
                                        >
                                            <i class="ri-upload-2-line" aria-hidden="true"></i>
                                            Select photo
                                        </button>

                                        @if ($this->user->profile_photo_path)
                                            <button
                                                type="button"
                                                wire:click="deleteProfilePhoto"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50"
                                            >
                                                <i class="ri-delete-bin-line" aria-hidden="true"></i>
                                                Remove
                                            </button>
                                        @endif
                                    </div>
                                    <x-input-error for="photo" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </x-account-settings.card>
                @endif

                <x-account-settings.card title="Personal Details" description="Your contact information for account notifications.">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-account-settings.field label="Full name" for="name" error="name" class="sm:col-span-2">
                            <input
                                id="name"
                                type="text"
                                wire:model="state.name"
                                required
                                autocomplete="name"
                                class="{{ $inputClass }}"
                            />
                        </x-account-settings.field>

                        <x-account-settings.field label="Email address" for="email" error="email" class="sm:col-span-2">
                            <input
                                id="email"
                                type="email"
                                wire:model="state.email"
                                required
                                autocomplete="username"
                                class="{{ $inputClass }}"
                            />

                            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                                <p class="mt-2 rounded-lg border border-amber-100 bg-amber-50 px-3 py-2 text-base text-amber-800">
                                    {{ __('Your email address is unverified.') }}
                                    <button
                                        type="button"
                                        class="font-medium underline hover:text-amber-900"
                                        wire:click.prevent="sendEmailVerification"
                                    >
                                        {{ __('Resend verification email') }}
                                    </button>
                                </p>

                                @if ($this->verificationLinkSent)
                                    <p class="mt-2 text-base font-medium text-green-600">
                                        {{ __('A new verification link has been sent to your email address.') }}
                                    </p>
                                @endif
                            @endif
                        </x-account-settings.field>

                        <x-account-settings.field label="Phone number" for="phone" error="phone" error-bag="updateProfileInformation" class="sm:col-span-2">
                            <div class="flex overflow-hidden rounded-lg border border-gray-300 focus-within:border-brand-600 focus-within:ring-1 focus-within:ring-brand-600">
                                <span class="inline-flex shrink-0 items-center border-r border-gray-300 bg-gray-50 px-3 text-base font-medium text-gray-600">
                                    {{ \App\Support\PhilippinePhone::PREFIX }}
                                </span>
                                <input
                                    id="phone"
                                    type="tel"
                                    wire:model="state.phone"
                                    inputmode="numeric"
                                    autocomplete="tel-national"
                                    placeholder="9XX XXX XXXX"
                                    maxlength="10"
                                    required
                                    class="block w-full border-0 px-3 py-2.5 text-base focus:border-0 focus:ring-0"
                                />
                            </div>
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
            form="store-profile-form"
            wire:loading.attr="disabled"
            wire:target="photo"
            class="inline-flex items-center gap-1.5 rounded-lg bg-forest-600 px-4 py-2.5 text-base font-semibold text-white hover:bg-forest-700 disabled:opacity-50"
        >
            <i class="ri-save-line" aria-hidden="true"></i>
            Save changes
        </button>
    </x-page-card.footer>
</div>
