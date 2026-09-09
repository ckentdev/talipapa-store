@php
    use App\Services\CartService;

    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart(auth()->user(), session()->getId());
    $cartCount = $cartService->getItemCount($cart);

    $ordersRoute = 'login';
    $ordersPatterns = [];
    $accountRoute = 'login';
    $accountPatterns = [];

    if (auth()->check()) {
        $user = auth()->user();

        if ($user->isCustomer()) {
            $ordersRoute = 'customer.orders.index';
            $ordersPatterns = ['customer.orders*', 'customer.checkout*'];
            $accountRoute = 'customer.account';
            $accountPatterns = ['customer.account*', 'customer.addresses*', 'account.show'];
        } elseif ($user->isStoreOwner()) {
            $ordersRoute = 'store.orders';
            $ordersPatterns = ['store.orders*'];
            $accountRoute = 'store.account';
            $accountPatterns = ['store.account*', 'store.information*'];
        } elseif ($user->isRider()) {
            $ordersRoute = 'rider.deliveries';
            $ordersPatterns = ['rider.deliveries*'];
            $accountRoute = 'rider.account';
            $accountPatterns = ['rider.account*'];
        } elseif ($user->isAdmin()) {
            $ordersRoute = 'admin.orders';
            $ordersPatterns = ['admin.orders*'];
            $accountRoute = 'admin.settings';
            $accountPatterns = ['admin.settings*'];
        }
    }

    $shopTab = [
        'label' => 'Shop',
        'route' => 'products.index',
        'icon' => 'products',
        'patterns' => ['products.*'],
    ];

    $storesTab = [
        'label' => 'Stores',
        'route' => 'stores.index',
        'icon' => 'store',
        'patterns' => ['stores.*'],
    ];

    $ordersTab = [
        'label' => 'Orders',
        'route' => $ordersRoute,
        'icon' => 'orders',
        'patterns' => $ordersPatterns,
    ];

    $middleTab = (auth()->check() && auth()->user()->isCustomer())
        ? $ordersTab
        : $storesTab;

    $publicNav = [
        [
            'label' => 'Home',
            'route' => 'landing',
            'icon' => 'home',
            'patterns' => ['landing'],
        ],
        $shopTab,
        $middleTab,
        [
            'label' => 'Cart',
            'route' => 'cart.index',
            'icon' => 'cart',
            'patterns' => ['cart.*', 'customer.checkout*'],
            'badge' => $cartCount > 0 ? $cartCount : null,
        ],
        [
            'label' => 'Account',
            'route' => $accountRoute,
            'icon' => 'account',
            'patterns' => auth()->check()
                ? $accountPatterns
                : ['login', 'join', 'register', 'register.*'],
        ],
    ];
@endphp

<x-mobile-tab-bar :items="$publicNav" />
