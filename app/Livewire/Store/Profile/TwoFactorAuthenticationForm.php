<?php

namespace App\Livewire\Store\Profile;

use Laravel\Jetstream\Http\Livewire\TwoFactorAuthenticationForm as BaseTwoFactorAuthenticationForm;

class TwoFactorAuthenticationForm extends BaseTwoFactorAuthenticationForm
{
    public function render()
    {
        return view('store.account.two-factor');
    }
}
