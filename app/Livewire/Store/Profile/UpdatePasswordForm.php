<?php

namespace App\Livewire\Store\Profile;

use Laravel\Jetstream\Http\Livewire\UpdatePasswordForm as BaseUpdatePasswordForm;

class UpdatePasswordForm extends BaseUpdatePasswordForm
{
    public function render()
    {
        return view('store.account.password');
    }
}
