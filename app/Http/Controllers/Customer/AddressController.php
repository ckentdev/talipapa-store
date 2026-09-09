<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Models\Address;
use App\Services\PsgcService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function __construct(
        private readonly PsgcService $psgcService,
    ) {}

    public function index(Request $request): View
    {
        $addresses = $request->user()
            ->addresses()
            ->with(['region', 'province', 'city', 'barangay'])
            ->latest()
            ->get();

        return view('customer.addresses.index', compact('addresses'));
    }

    public function create(): View
    {
        return view('customer.addresses.create', [
            'regions' => $this->psgcService->getRegions(),
        ]);
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if ($request->boolean('is_default') || $request->user()->addresses()->count() === 0) {
            $request->user()->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        Address::query()->create($data);

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Address saved.');
    }

    public function edit(Request $request, Address $address): View
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $address->load(['region', 'province', 'city', 'barangay']);

        return view('customer.addresses.edit', [
            'address' => $address,
            'regions' => $this->psgcService->getRegions(),
        ]);
    }

    public function update(UpdateAddressRequest $request, Address $address): RedirectResponse
    {
        $data = $request->validated();

        if ($request->boolean('is_default')) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $address->update($data);

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Address updated.');
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $address->delete();

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Address removed.');
    }
}
