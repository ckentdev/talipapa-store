<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function show(Request $request): View
    {
        return view('customer.account', [
            'user' => $request->user(),
        ]);
    }
}
