<?php

namespace App\Livewire\Profile;

use App\Enums\UserRole;
use App\Support\PhilippinePhone;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm as BaseUpdateProfileInformationForm;

class UpdateProfileInformationForm extends BaseUpdateProfileInformationForm
{
    public function mount(): void
    {
        $user = Auth::user();

        $this->state = array_merge(
            $user->withoutRelations()->toArray(),
            [
                'email' => $user->email,
                'phone' => PhilippinePhone::localPart($user->phone),
            ]
        );
    }

    public function updateProfileInformation(UpdatesUserProfileInformation $updater)
    {
        $this->resetErrorBag();

        $updater->update(
            Auth::user(),
            $this->photo
                ? array_merge($this->state, ['photo' => $this->photo])
                : $this->state
        );

        if (isset($this->photo)) {
            return redirect()->to($this->accountUrl());
        }

        $this->dispatch('saved');
        $this->dispatch('refresh-navigation-menu');
    }

    protected function accountUrl(): string
    {
        return match (Auth::user()->role) {
            UserRole::StoreOwner => route('store.account'),
            UserRole::Rider => route('rider.account'),
            default => route('profile.show'),
        };
    }
}
