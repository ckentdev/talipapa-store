<?php

namespace App\Livewire\Store\Profile;

use Laravel\Jetstream\Http\Livewire\LogoutOtherBrowserSessionsForm as BaseLogoutOtherBrowserSessionsForm;

class LogoutOtherBrowserSessionsForm extends BaseLogoutOtherBrowserSessionsForm
{
    public function render()
    {
        return view('store.account.sessions');
    }
}
