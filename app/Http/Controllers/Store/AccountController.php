<?php

namespace App\Http\Controllers\Store;

use App\Enums\ApprovalStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $store = $user->storeProfile;
        $isApproved = $store && $store->status === ApprovalStatus::Approved;

        return view('store.account', compact('user', 'store', 'isApproved'));
    }
}
