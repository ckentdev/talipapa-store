<?php

namespace App\Livewire\Store\Profile;

use App\Livewire\Profile\UpdateProfileInformationForm as BaseUpdateProfileInformationForm;

class UpdateProfileInformationForm extends BaseUpdateProfileInformationForm
{
    public function render()
    {
        return view('store.account.profile-information');
    }
}
