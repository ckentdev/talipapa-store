<?php

namespace App\Http\Controllers\Public;

use App\Enums\ApprovalStatus;
use App\Http\Controllers\Controller;
use App\Models\StoreProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(Request $request): View
    {
        $stores = StoreProfile::query()
            ->approved()
            ->with('addresses')
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('store_name', 'like', '%'.$request->string('q').'%');
            })
            ->orderBy('store_name')
            ->paginate(12)
            ->withQueryString();

        return view('public.stores.index', compact('stores'));
    }

    public function show(StoreProfile $store): View
    {
        abort_unless($store->status === ApprovalStatus::Approved, 404);

        $store->load(['addresses', 'products' => fn ($query) => $query->available()->with('category')]);

        return view('public.stores.show', compact('store'));
    }
}
