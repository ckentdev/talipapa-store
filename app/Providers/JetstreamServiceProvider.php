<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use App\Livewire\Profile\UpdateProfileInformationForm;
use App\Livewire\Store\Profile\DeleteUserForm as StoreDeleteUserForm;
use App\Livewire\Store\Profile\LogoutOtherBrowserSessionsForm as StoreLogoutOtherBrowserSessionsForm;
use App\Livewire\Store\Profile\TwoFactorAuthenticationForm as StoreTwoFactorAuthenticationForm;
use App\Livewire\Store\Profile\UpdatePasswordForm as StoreUpdatePasswordForm;
use App\Livewire\Store\Profile\UpdateProfileInformationForm as StoreUpdateProfileInformationForm;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Livewire\Livewire;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);

        Livewire::component('profile.update-profile-information-form', UpdateProfileInformationForm::class);

        Livewire::component('store.profile.update-profile-information-form', StoreUpdateProfileInformationForm::class);
        Livewire::component('store.profile.update-password-form', StoreUpdatePasswordForm::class);
        Livewire::component('store.profile.two-factor-authentication-form', StoreTwoFactorAuthenticationForm::class);
        Livewire::component('store.profile.logout-other-browser-sessions-form', StoreLogoutOtherBrowserSessionsForm::class);
        Livewire::component('store.profile.delete-user-form', StoreDeleteUserForm::class);
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
