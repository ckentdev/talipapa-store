@extends('layouts.store')

@section('title', 'Dashboard')

@section('content')
@php
    use App\Enums\ApprovalStatus;

    $hour = (int) now()->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $isApproved = $store && $store->status === ApprovalStatus::Approved;
    $statusClasses = match ($store?->status) {
        ApprovalStatus::Approved => 'bg-green-100 text-green-800',
        ApprovalStatus::Pending => 'bg-yellow-100 text-yellow-800',
        ApprovalStatus::Rejected => 'bg-red-100 text-red-800',
        ApprovalStatus::Suspended => 'bg-orange-100 text-orange-800',
        default => 'bg-gray-100 text-gray-800',
    };

    $statCards = [
        [
            'label' => 'Pending Orders',
            'value' => $stats['pending_orders'],
            'icon' => 'ri-time-line',
            'iconBg' => 'bg-yellow-100 text-yellow-700',
            'valueClass' => 'text-yellow-700',
            'hint' => 'Awaiting your response',
        ],
        [
            'label' => 'Active Orders',
            'value' => $stats['active_orders'],
            'icon' => 'ri-shopping-bag-3-line',
            'iconBg' => 'bg-blue-100 text-blue-700',
            'valueClass' => 'text-blue-700',
            'hint' => 'In progress right now',
        ],
        [
            'label' => 'Products',
            'value' => $stats['total_products'],
            'icon' => 'ri-store-2-line',
            'iconBg' => 'bg-forest-100 text-forest-700',
            'valueClass' => 'text-gray-900',
            'hint' => 'Listed in your store',
        ],
        [
            'label' => "Today's Sales",
            'value' => '₱'.number_format($stats['today_sales'], 2),
            'icon' => 'ri-money-dollar-circle-line',
            'iconBg' => 'bg-brand-100 text-brand-700',
            'valueClass' => 'text-brand-700',
            'hint' => 'Delivered orders today',
        ],
    ];

    $quickActions = [
        [
            'label' => 'Manage Orders',
            'description' => 'Accept, prepare, and fulfill orders',
            'route' => 'store.orders',
            'icon' => 'ri-file-list-3-line',
            'badge' => $stats['pending_orders'] > 0 ? $stats['pending_orders'] : null,
        ],
        [
            'label' => 'Products & Inventory',
            'description' => $isApproved ? 'Add and update your catalog' : 'View your product list',
            'route' => 'store.products',
            'icon' => 'ri-shopping-basket-line',
        ],
        [
            'label' => 'In App Point of Sales',
            'description' => 'Ring up walk-in customers in-store',
            'route' => 'store.pos.index',
            'icon' => 'ri-cash-line',
        ],
        [
            'label' => 'Sales Reports',
            'description' => 'Track revenue and performance',
            'route' => 'store.reports.index',
            'icon' => 'ri-bar-chart-box-line',
        ],
        [
            'label' => 'Account Settings',
            'description' => 'Profile, password, and security',
            'route' => 'store.account',
            'icon' => 'ri-user-settings-line',
        ],
        [
            'label' => 'Store Information',
            'description' => 'Store details, logo, and address',
            'route' => 'store.information',
            'icon' => 'ri-store-2-line',
        ],
    ];
@endphp

@if ($store && ! $isApproved)
    <x-alert type="warning" title="Store under review" class="mb-6">
        Your store is <strong>{{ $store->status->value }}</strong>.
        @if ($store->rejection_reason)
            Reason: {{ $store->rejection_reason }}
        @else
            You will be notified once an admin reviews your application.
        @endif
    </x-alert>
@endif

{{-- Welcome hero --}}
<section class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-gradient-to-r from-avocado-50/80 via-white to-white shadow-sm">
    <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
        <div class="flex min-w-0 items-start gap-4">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-avocado-200/70">
                @if ($store && filled($store->logo_path))
                    <x-img
                        :src="$store->logoUrl()"
                        :alt="$store->store_name"
                        type="store_logo"
                        class="h-full w-full object-cover"
                    />
                @else
                    <span class="text-2xl font-bold text-forest-600">
                        {{ strtoupper(substr($store?->store_name ?? auth()->user()->name, 0, 1)) }}
                    </span>
                @endif
            </div>

            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-wider text-brand-600">Store Dashboard</p>
                <h2 class="mt-1 truncate text-xl font-bold text-gray-900 sm:text-2xl">
                    {{ $greeting }}, {{ auth()->user()->name }}
                </h2>
                <div class="mt-1 flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1 text-sm">
                    <span class="truncate font-medium text-gray-600">
                        {{ $store?->store_name ?? 'Your store' }}
                    </span>
                    @if ($store)
                        <span class="text-gray-300" aria-hidden="true">|</span>
                        <span @class(['inline-flex shrink-0 items-center rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize', $statusClasses])>
                            {{ $store->status->value }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if ($stats['pending_orders'] > 0)
            <a
                href="{{ route('store.orders') }}"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-forest-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-forest-700"
            >
                <i class="ri-notification-3-line text-lg" aria-hidden="true"></i>
                {{ $stats['pending_orders'] }} pending {{ str('order')->plural($stats['pending_orders']) }}
            </a>
        @endif
    </div>
</section>

{{-- KPI cards --}}
<section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($statCards as $card)
        <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-avocado-200 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-500">{{ $card['label'] }}</p>
                    <p @class(['mt-2 text-2xl font-bold tracking-tight', $card['valueClass']])>{{ $card['value'] }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ $card['hint'] }}</p>
                </div>
                <span @class(['inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl', $card['iconBg']])>
                    <i class="{{ $card['icon'] }} text-xl" aria-hidden="true"></i>
                </span>
            </div>
        </article>
    @endforeach
</section>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    {{-- Recent orders --}}
    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm xl:col-span-2">
        <div class="flex items-center justify-between border-b border-gray-100 bg-gradient-to-r from-avocado-50/50 to-white px-5 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-forest-600/10 text-forest-600">
                    <i class="ri-shopping-cart-2-line text-lg" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="font-bold text-gray-900">Recent Orders</h2>
                    <p class="text-xs text-gray-500">Latest activity from your store</p>
                </div>
            </div>
            <a href="{{ route('store.orders') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700">
                View all
                <i class="ri-arrow-right-s-line text-base" aria-hidden="true"></i>
            </a>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse ($recentOrders as $order)
                <a href="{{ route('store.orders.show', $order) }}" class="group flex flex-col gap-3 px-5 py-4 transition hover:bg-avocado-50/40 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-gray-900 group-hover:text-brand-700">{{ $order->order_number }}</p>
                            <span class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-0.5 truncate text-sm text-gray-600">{{ $order->displayCustomerName() }}</p>
                        <p class="mt-1 text-xs text-gray-400">
                            {{ $order->items->count() }} {{ str('item')->plural($order->items->count()) }}
                            · ₱{{ number_format($order->total, 2) }}
                        </p>
                    </div>
                    <x-order-status-badge :status="$order->status" class="self-start sm:self-center" />
                </a>
            @empty
                <div class="p-6">
                    @include('layouts.partials._empty_state', [
                        'title' => 'No orders yet',
                        'message' => 'When customers place orders, they will appear here.',
                        'icon' => '<i class="ri-shopping-bag-3-line text-2xl" aria-hidden="true"></i>',
                    ])
                </div>
            @endforelse
        </div>
    </section>

    {{-- Sidebar --}}
    <aside class="space-y-6">
        {{-- Quick actions --}}
        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="font-bold text-gray-900">Quick Actions</h2>
                <p class="text-xs text-gray-500">Jump to common tasks</p>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($quickActions as $action)
                    <a href="{{ route($action['route']) }}" class="group flex items-start gap-3 px-5 py-4 transition hover:bg-avocado-50/40">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-600 transition group-hover:bg-forest-600/10 group-hover:text-forest-600">
                            <i class="{{ $action['icon'] }} text-lg" aria-hidden="true"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="flex items-center gap-2">
                                <span class="font-semibold text-gray-900 group-hover:text-brand-700">{{ $action['label'] }}</span>
                                @if (! empty($action['badge']))
                                    <span class="inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-yellow-100 px-1.5 py-0.5 text-[10px] font-bold text-yellow-800">
                                        {{ $action['badge'] }}
                                    </span>
                                @endif
                            </span>
                            <span class="mt-0.5 block text-xs text-gray-500">{{ $action['description'] }}</span>
                        </span>
                        <i class="ri-arrow-right-s-line shrink-0 text-lg text-gray-300 transition group-hover:text-brand-600" aria-hidden="true"></i>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Store snapshot --}}
        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="font-bold text-gray-900">Store Snapshot</h2>
            </div>
            <dl class="divide-y divide-gray-100 px-5 py-1 text-sm">
                <div class="flex items-center justify-between py-3">
                    <dt class="text-gray-500">Status</dt>
                    <dd>
                        @if ($store)
                            <span @class(['inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize', $statusClasses])>
                                {{ $store->status->value }}
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-center justify-between py-3">
                    <dt class="text-gray-500">Products listed</dt>
                    <dd class="font-semibold text-gray-900">{{ $stats['total_products'] }}</dd>
                </div>
                <div class="flex items-center justify-between py-3">
                    <dt class="text-gray-500">Active orders</dt>
                    <dd class="font-semibold text-blue-700">{{ $stats['active_orders'] }}</dd>
                </div>
                <div class="flex items-center justify-between py-3">
                    <dt class="text-gray-500">Today's revenue</dt>
                    <dd class="font-semibold text-brand-700">₱{{ number_format($stats['today_sales'], 2) }}</dd>
                </div>
            </dl>
        </section>

        @if (! $isApproved)
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <div class="flex gap-3">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <i class="ri-information-line text-lg" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h3 class="font-semibold text-amber-900">Getting started</h3>
                        <p class="mt-1 text-sm text-amber-800">
                            Once your store is approved, you can add products and start receiving orders from customers.
                        </p>
                    </div>
                </div>
            </section>
        @endif
    </aside>
</div>
@endsection
