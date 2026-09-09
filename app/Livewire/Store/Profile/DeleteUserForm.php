<?php

namespace App\Livewire\Store\Profile;

use Laravel\Jetstream\Http\Livewire\DeleteUserForm as BaseDeleteUserForm;

class DeleteUserForm extends BaseDeleteUserForm
{
    public function render()
    {
        return view('store.account.delete');
    }
}
