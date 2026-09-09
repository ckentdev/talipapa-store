<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'delivery_fee' => PlatformSetting::get('delivery_fee', 50),
            'platform_name' => PlatformSetting::get('platform_name', 'RepoMart'),
            'support_email' => PlatformSetting::get('support_email', 'support@repomart.test'),
            'min_order_amount' => PlatformSetting::get('min_order_amount', 0),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_fee' => 'required|numeric|min:0',
            'platform_name' => 'required|string|max:255',
            'support_email' => 'required|email|max:255',
            'min_order_amount' => 'required|numeric|min:0',
        ]);

        foreach ($validated as $key => $value) {
            PlatformSetting::set($key, $value);
        }

        return back()->with('success', 'Settings updated.');
    }
}
