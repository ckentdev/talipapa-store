@extends('layouts.store')

@section('title', 'Account Settings')

@section('content')
@php
    $tabs = collect([
        ['id' => 'profile', 'label' => 'Profile', 'icon' => 'ri-user-3-line', 'enabled' => Laravel\Fortify\Features::canUpdateProfileInformation()],
        ['id' => 'password', 'label' => 'Password', 'icon' => 'ri-lock-password-line', 'enabled' => Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords())],
        ['id' => 'two-factor', 'label' => 'Two-Factor', 'icon' => 'ri-shield-keyhole-line', 'enabled' => Laravel\Fortify\Features::canManageTwoFactorAuthentication()],
        ['id' => 'sessions', 'label' => 'Sessions', 'icon' => 'ri-device-line', 'enabled' => true],
        ['id' => 'delete', 'label' => 'Delete Account', 'icon' => 'ri-delete-bin-line', 'enabled' => Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures()],
    ])->filter(fn ($tab) => $tab['enabled'])->values();

    $activeTab = request('tab', $tabs->first()['id'] ?? 'profile');
    if (! $tabs->contains(fn ($tab) => $tab['id'] === $activeTab)) {
        $activeTab = $tabs->first()['id'] ?? 'profile';
    }

    $tabBtnClass = fn (string $tab) => $activeTab === $tab
        ? 'inline-flex items-center gap-2 border-b-2 border-forest-600 px-3 py-2.5 text-base font-semibold text-forest-700 sm:px-4 sm:py-3'
        : 'inline-flex items-center gap-2 border-b-2 border-transparent px-3 py-2.5 text-base font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 sm:px-4 sm:py-3';
@endphp

<section class="mb-6">
    <div class="mb-4">
        <h2 class="text-lg font-bold text-gray-900">Store Modules</h2>
        <p class="text-sm text-gray-500">Tools and apps available for your store account.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <a
            href="{{ route('store.pos.index') }}"
            class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-forest-200 hover:shadow-md"
        >
            <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-forest-600/10 text-forest-600 transition group-hover:bg-forest-600 group-hover:text-white">
                <i class="ri-cash-line text-xl" aria-hidden="true"></i>
            </span>
            <span class="min-w-0">
                <span class="flex items-center gap-2">
                    <span class="font-semibold text-gray-900 group-hover:text-brand-700">In App Point of Sales</span>
                    @if ($isApproved)
                        <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-green-700">Active</span>
                    @else
                        <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-700">Pending approval</span>
                    @endif
                </span>
                <span class="mt-1 block text-sm text-gray-500">Ring up walk-in customers, track stock, and record in-store sales.</span>
            </span>
            <i class="ri-arrow-right-s-line ml-auto shrink-0 text-xl text-gray-300 transition group-hover:text-brand-600" aria-hidden="true"></i>
        </a>
    </div>
</section>

<div class="w-full">
    <div class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 bg-gradient-to-r from-avocado-50/80 to-white px-4 py-3 sm:px-5">
            <div class="flex min-w-0 items-center gap-3">
                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-forest-600/10 text-forest-600">
                    <i class="ri-user-settings-line text-lg" aria-hidden="true"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="truncate text-base font-bold text-gray-900">Account Settings</h2>
                    <p class="truncate text-base text-gray-500">Manage your login, security, and profile.</p>
                </div>
            </div>
            <a href="{{ route('store.dashboard') }}" class="inline-flex shrink-0 items-center gap-1 text-base font-medium text-gray-500 hover:text-brand-600">
                <i class="ri-arrow-left-line" aria-hidden="true"></i>
                Dashboard
            </a>
        </div>

        <div class="border-b border-gray-200 px-4 sm:px-5">
            <ul
                class="-mb-px flex flex-wrap gap-1 overflow-x-auto"
                id="account-settings-tabs"
                data-tabs-toggle="#account-settings-tab-content"
                role="tablist"
            >
                @foreach ($tabs as $tab)
                    <li role="presentation">
                        <button
                            type="button"
                            id="{{ $tab['id'] }}-tab"
                            data-tabs-target="#{{ $tab['id'] }}-panel"
                            role="tab"
                            aria-controls="{{ $tab['id'] }}-panel"
                            aria-selected="{{ $activeTab === $tab['id'] ? 'true' : 'false' }}"
                            class="{{ $tabBtnClass($tab['id']) }}"
                        >
                            <i class="{{ $tab['icon'] }} text-base" aria-hidden="true"></i>
                            {{ $tab['label'] }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div id="account-settings-tab-content" class="flex flex-col">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div
                    id="profile-panel"
                    role="tabpanel"
                    aria-labelledby="profile-tab"
                    @class(['flex flex-col', 'hidden' => $activeTab !== 'profile'])
                >
                    @livewire('store.profile.update-profile-information-form')
                </div>
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div
                    id="password-panel"
                    role="tabpanel"
                    aria-labelledby="password-tab"
                    @class(['flex flex-col', 'hidden' => $activeTab !== 'password'])
                >
                    @livewire('store.profile.update-password-form')
                </div>
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div
                    id="two-factor-panel"
                    role="tabpanel"
                    aria-labelledby="two-factor-tab"
                    @class(['flex flex-col', 'hidden' => $activeTab !== 'two-factor'])
                >
                    @livewire('store.profile.two-factor-authentication-form')
                </div>
            @endif

            <div
                id="sessions-panel"
                role="tabpanel"
                aria-labelledby="sessions-tab"
                @class(['flex flex-col', 'hidden' => $activeTab !== 'sessions'])
            >
                @livewire('store.profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <div
                    id="delete-panel"
                    role="tabpanel"
                    aria-labelledby="delete-tab"
                    @class(['flex flex-col', 'hidden' => $activeTab !== 'delete'])
                >
                    @livewire('store.profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if ($activeTab !== ($tabs->first()['id'] ?? 'profile'))
            document.getElementById('{{ $activeTab }}-tab')?.click();
        @endif
    });
</script>
@endpush
