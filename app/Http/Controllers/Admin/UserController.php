<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Events\UserStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->with(['storeProfile', 'riderProfile', 'customerProfile']);

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load(['storeProfile', 'riderProfile', 'customerProfile', 'orders']);

        return view('admin.users.show', compact('user'));
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $previous = $user->status;
        $newStatus = UserStatus::from($validated['status']);

        $user->update(['status' => $newStatus]);

        UserStatusChanged::dispatch($user, $previous, $newStatus);

        return back()->with('success', 'User status updated.');
    }
}
