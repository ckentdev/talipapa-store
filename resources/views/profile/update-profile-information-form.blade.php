<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6">
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <div class="flex flex-col gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4 sm:flex-row sm:items-center">
                    <div class="shrink-0" x-show="photoPreview" style="display: none;">
                        <span class="block h-24 w-24 rounded-full bg-cover bg-center bg-no-repeat ring-4 ring-white shadow-md"
                              x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <x-label for="photo" value="{{ __('Profile Photo') }}" />

                        <p class="mt-1 text-base text-gray-500">{{ __('Select a new photo to update your avatar. JPG, PNG, or GIF up to 1MB.') }}</p>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <x-secondary-button type="button" x-on:click.prevent="$refs.photo.click()">
                                {{ __('Select A New Photo') }}
                            </x-secondary-button>

                            @if ($this->user->profile_photo_path)
                                <x-secondary-button type="button" wire:click="deleteProfilePhoto">
                                    {{ __('Remove Photo') }}
                                </x-secondary-button>
                            @endif
                        </div>

                        <x-input-error for="photo" class="mt-2" />
                    </div>
                </div>
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>

        <!-- Phone -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="phone" value="{{ __('Phone') }}" />
            <div class="mt-1 flex overflow-hidden rounded-md border border-gray-300 shadow-sm focus-within:border-indigo-500 focus-within:ring-indigo-500">
                <span class="inline-flex shrink-0 items-center border-r border-gray-300 bg-gray-50 px-3 text-sm font-medium text-gray-600">{{ \App\Support\PhilippinePhone::PREFIX }}</span>
                <input
                    id="phone"
                    type="tel"
                    wire:model="state.phone"
                    inputmode="numeric"
                    autocomplete="tel-national"
                    placeholder="9XX XXX XXXX"
                    maxlength="10"
                    required
                    class="block w-full border-0 focus:border-0 focus:ring-0"
                />
            </div>
            <x-input-error for="phone" bag="updateProfileInformation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
