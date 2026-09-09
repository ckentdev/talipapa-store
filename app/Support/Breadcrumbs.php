<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class Breadcrumbs
{
    private const DASHBOARD_ROUTES = [
        'store' => 'store.dashboard',
        'admin' => 'admin.dashboard',
        'rider' => 'rider.dashboard',
    ];

    /** @var array<string, string|null> */
    private const PARENT_ROUTES = [
        'store.orders.show' => 'store.orders',
        'store.products.create' => 'store.products',
        'store.products.edit' => 'store.products',
        'admin.users.show' => 'admin.users',
        'admin.orders.show' => 'admin.orders',
        'admin.categories.create' => 'admin.categories.index',
        'admin.categories.edit' => 'admin.categories.index',
    ];

    /** @var array<string, string> */
    private const ROUTE_SECTION_LABELS = [
        'admin.orders' => 'Customer Orders',
        'store.orders' => 'Customer Orders',
    ];

    /** @var array<string, string> */
    private const SECTION_LABELS = [
        'dashboard' => 'Dashboard',
        'orders' => 'Orders',
        'products' => 'Products & Inventory',
        'riders' => 'Available Riders',
        'reports' => 'Sales Report',
        'account' => 'Account Settings',
        'information' => 'Store Information',
        'deliveries' => 'Deliveries',
        'earnings' => 'Earnings',
        'location' => 'Location',
        'users' => 'Users',
        'approvals' => 'Approvals',
        'categories' => 'Categories',
        'settings' => 'Settings',
        'map' => 'Location Map',
        'psgc' => 'PSGC Data',
    ];

    /**
     * @return list<array{label: string, url?: string}>
     */
    public static function resolve(string $currentTitle): array
    {
        $routeName = Route::currentRouteName();
        if (! $routeName) {
            return [['label' => $currentTitle]];
        }

        $parts = explode('.', $routeName);
        $prefix = $parts[0] ?? null;
        $dashboardRoute = self::DASHBOARD_ROUTES[$prefix] ?? null;

        if (! $dashboardRoute) {
            return [['label' => $currentTitle]];
        }

        if ($routeName === $dashboardRoute) {
            return [['label' => $currentTitle]];
        }

        $items = [[
            'label' => 'Dashboard',
            'url' => route($dashboardRoute),
        ]];

        $parentRoute = self::parentRoute($routeName, $parts);
        if ($parentRoute && Route::has($parentRoute)) {
            $parentParts = explode('.', $parentRoute);
            $section = $parentParts[1] ?? '';
            $prefix = $parentParts[0] ?? '';
            $items[] = [
                'label' => self::sectionLabel($prefix, $section, $parentRoute),
                'url' => route($parentRoute),
            ];
        }

        $items[] = ['label' => $currentTitle];

        return $items;
    }

    /**
     * @param  list<string>  $parts
     */
    private static function parentRoute(string $routeName, array $parts): ?string
    {
        if (array_key_exists($routeName, self::PARENT_ROUTES)) {
            return self::PARENT_ROUTES[$routeName];
        }

        $prefix = $parts[0] ?? null;
        $action = $parts[count($parts) - 1] ?? null;
        $resource = $parts[1] ?? null;

        if (! $prefix || ! $resource || ! $action) {
            return null;
        }

        if (in_array($action, ['create', 'edit', 'show'], true)) {
            $indexRoute = "{$prefix}.{$resource}.index";
            if (Route::has($indexRoute)) {
                return $indexRoute;
            }

            $listRoute = "{$prefix}.{$resource}";
            if (Route::has($listRoute)) {
                return $listRoute;
            }
        }

        return null;
    }

    private static function sectionLabel(string $prefix, string $section, string $parentRoute): string
    {
        if (isset(self::ROUTE_SECTION_LABELS[$parentRoute])) {
            return self::ROUTE_SECTION_LABELS[$parentRoute];
        }

        return self::SECTION_LABELS[$section] ?? ucfirst(str_replace('_', ' ', $section));
    }
}
