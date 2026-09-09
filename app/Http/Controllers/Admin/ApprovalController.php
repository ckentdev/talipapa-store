<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApprovalStatus;
use App\Events\ApprovalStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\RiderProfile;
use App\Models\StoreProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function index(): View
    {
        $pendingStores = StoreProfile::query()
            ->where('status', ApprovalStatus::Pending)
            ->with(['user', 'requirements', 'addresses'])
            ->latest()
            ->get();

        $pendingRiders = RiderProfile::query()
            ->where('status', ApprovalStatus::Pending)
            ->with(['user', 'requirements', 'addresses'])
            ->latest()
            ->get();

        return view('admin.approvals.index', compact('pendingStores', 'pendingRiders'));
    }

    public function approveStore(StoreProfile $store): RedirectResponse
    {
        $previous = $store->status;

        $store->update([
            'status' => ApprovalStatus::Approved,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        ApprovalStatusChanged::dispatch($store, $previous, ApprovalStatus::Approved);

        return back()->with('success', "Store \"{$store->store_name}\" approved.");
    }

    public function rejectStore(Request $request, StoreProfile $store): RedirectResponse
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $previous = $store->status;

        $store->update([
            'status' => ApprovalStatus::Rejected,
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        ApprovalStatusChanged::dispatch($store, $previous, ApprovalStatus::Rejected);

        return back()->with('success', 'Store application rejected.');
    }

    public function suspendStore(Request $request, StoreProfile $store): RedirectResponse
    {
        $previous = $store->status;

        $store->update(['status' => ApprovalStatus::Suspended]);

        ApprovalStatusChanged::dispatch($store, $previous, ApprovalStatus::Suspended);

        return back()->with('success', 'Store suspended.');
    }

    public function approveRider(RiderProfile $rider): RedirectResponse
    {
        $previous = $rider->status;

        $rider->update([
            'status' => ApprovalStatus::Approved,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        ApprovalStatusChanged::dispatch($rider, $previous, ApprovalStatus::Approved);

        return back()->with('success', "Rider \"{$rider->user->name}\" approved.");
    }

    public function rejectRider(Request $request, RiderProfile $rider): RedirectResponse
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $previous = $rider->status;

        $rider->update([
            'status' => ApprovalStatus::Rejected,
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        ApprovalStatusChanged::dispatch($rider, $previous, ApprovalStatus::Rejected);

        return back()->with('success', 'Rider application rejected.');
    }

    public function suspendRider(RiderProfile $rider): RedirectResponse
    {
        $previous = $rider->status;

        $rider->update(['status' => ApprovalStatus::Suspended]);

        ApprovalStatusChanged::dispatch($rider, $previous, ApprovalStatus::Suspended);

        return back()->with('success', 'Rider suspended.');
    }
}
