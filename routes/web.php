<?php

use App\Http\Controllers\Account\PermissionController;
use App\Http\Controllers\Admin\ApprovalController as AdminApprovalController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MapController as AdminMapController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PsgcController as AdminPsgcController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VocController as AdminVocController;
use App\Http\Controllers\Auth\CustomerRegistrationController;
use App\Http\Controllers\Auth\RegistrationHubController;
use App\Http\Controllers\Auth\RiderRegistrationController;
use App\Http\Controllers\Auth\StoreRegistrationController;
use App\Http\Controllers\Customer\AccountController as CustomerAccountController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\CustomerHomeController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\LandingController;
use App\Http\Controllers\Public\NewsletterSubscribeController;
use App\Http\Controllers\Public\ProductController as PublicProductController;
use App\Http\Controllers\Public\RobotsController;
use App\Http\Controllers\Public\SitemapController;
use App\Http\Controllers\Public\StoreController as PublicStoreController;
use App\Http\Controllers\PsgcController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\VoiceAssistantController;
use App\Http\Controllers\VoiceSearchController;
use App\Http\Controllers\Rider\AccountController as RiderAccountController;
use App\Http\Controllers\Rider\DashboardController as RiderDashboardController;
use App\Http\Controllers\Rider\DeliveryController;
use App\Http\Controllers\Rider\EarningsController;
use App\Http\Controllers\Rider\LocationController;
use App\Http\Controllers\Store\AccountController as StoreAccountController;
use App\Http\Controllers\Store\StoreInformationController;
use App\Http\Controllers\Store\DashboardController as StoreDashboardController;
use App\Http\Controllers\Store\OrderController as StoreOrderController;
use App\Http\Controllers\Store\PosController as StorePosController;
use App\Http\Controllers\Store\ProductController as StoreProductController;
use App\Http\Controllers\Store\ReportController as StoreReportController;
use App\Http\Controllers\Store\RiderController as StoreRiderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/join', [RegistrationHubController::class, 'show'])->name('join');

Route::get('/stores', [PublicStoreController::class, 'index'])->name('stores.index');
Route::get('/stores/{store}', [PublicStoreController::class, 'show'])->name('stores.show');
Route::get('/products', [PublicProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [PublicProductController::class, 'show'])->name('products.show');

Route::get('/psgc/reverse-geocode', [PsgcController::class, 'reverseGeocode'])->name('psgc.reverse-geocode');
Route::get('/psgc/provinces/{regionCode}', [PsgcController::class, 'provinces']);
Route::get('/psgc/cities/{provinceCode}', [PsgcController::class, 'cities']);
Route::get('/psgc/barangays/{cityCode}', [PsgcController::class, 'barangays']);

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/preview', [CartController::class, 'preview'])->name('cart.preview');
Route::post('/newsletter/subscribe', [NewsletterSubscribeController::class, 'store'])->name('newsletter.subscribe');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::middleware(['throttle:voice-assistant'])->group(function () {
    Route::post('/voice-assistant/process', [VoiceAssistantController::class, 'process'])
        ->name('voice-assistant.process');
    Route::post('/voice-search/transcribe', [VoiceSearchController::class, 'transcribe'])
        ->name('voice-search.transcribe');
    Route::post('/voice-search/speak', [VoiceSearchController::class, 'speak'])
        ->name('voice-search.speak');
});

Route::prefix('register')->group(function () {
    Route::get('/store/{step?}', [StoreRegistrationController::class, 'show'])->name('register.store');
    Route::post('/store/step/{step}', [StoreRegistrationController::class, 'storeStep'])->name('register.store.step');
    Route::post('/store/submit', [StoreRegistrationController::class, 'submit'])->name('register.store.submit');

    Route::get('/rider/{step?}', [RiderRegistrationController::class, 'show'])->name('register.rider');
    Route::post('/rider/step/{step}', [RiderRegistrationController::class, 'storeStep'])->name('register.rider.step');
    Route::post('/rider/submit', [RiderRegistrationController::class, 'submit'])->name('register.rider.submit');
});

Route::get('/register/{step?}', [CustomerRegistrationController::class, 'show'])->name('register');
Route::post('/register/step/{step}', [CustomerRegistrationController::class, 'storeStep'])->name('register.step');
Route::post('/register/submit', [CustomerRegistrationController::class, 'submit'])->name('register.submit');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route(auth()->user()->role->dashboardRoute()))->name('dashboard');

    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::delete('/push/subscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
    Route::post('/account/permissions', [PermissionController::class, 'update'])->name('account.permissions.update');
    Route::patch('/account/sound-alerts', [PermissionController::class, 'updateSoundAlerts'])->name('account.sound-alerts.update');
    Route::get('/account', [CustomerAccountController::class, 'show'])->name('account.show');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');

    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/home', fn () => redirect()->route('landing'))->name('home');
        Route::get('/cart', fn () => redirect()->route('cart.index'))->name('cart');
        Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
        Route::get('/account', [CustomerAccountController::class, 'show'])->name('account');
        Route::resource('addresses', AddressController::class)->except(['show']);
        Route::get('/checkout/{step?}', [CheckoutController::class, 'show'])->name('checkout.show');
        Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
        Route::post('/orders/{order}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    });

    Route::middleware('role:store_owner')->prefix('store')->name('store.')->group(function () {
        Route::get('/dashboard', [StoreDashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [StoreOrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [StoreOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/accept', [StoreOrderController::class, 'accept'])->name('orders.accept');
        Route::post('/orders/{order}/reject', [StoreOrderController::class, 'reject'])->name('orders.reject');
        Route::post('/orders/{order}/preparing', [StoreOrderController::class, 'preparing'])->name('orders.preparing');
        Route::post('/orders/{order}/ready', [StoreOrderController::class, 'ready'])->name('orders.ready');
        Route::post('/orders/{order}/assign-rider', [StoreOrderController::class, 'assignRider'])->name('orders.assign-rider');
        Route::get('/riders', [StoreRiderController::class, 'index'])->name('riders');
        Route::get('/reports', [StoreReportController::class, 'index'])->name('reports.index');
        Route::get('/account', [StoreAccountController::class, 'show'])->name('account');
        Route::get('/information', [StoreInformationController::class, 'show'])->name('information');
        Route::put('/information', [StoreInformationController::class, 'update'])->name('information.update');
        Route::get('/products', [StoreProductController::class, 'index'])->name('products');
        Route::get('/pos', [StorePosController::class, 'index'])->name('pos.index');

        Route::middleware('store.approved')->group(function () {
            Route::post('/pos/checkout', [StorePosController::class, 'checkout'])->name('pos.checkout');
            Route::get('/products/create', [StoreProductController::class, 'create'])->name('products.create');
            Route::post('/products', [StoreProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}/edit', [StoreProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{product}', [StoreProductController::class, 'update'])->name('products.update');
            Route::delete('/products/{product}', [StoreProductController::class, 'destroy'])->name('products.destroy');
        });
    });

    Route::middleware('role:rider')->prefix('rider')->name('rider.')->group(function () {
        Route::get('/dashboard', [RiderDashboardController::class, 'index'])->name('dashboard');
        Route::post('/availability', [RiderDashboardController::class, 'toggleAvailability'])->name('availability.toggle');
        Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries');
        Route::post('/deliveries/{order}/accept', [DeliveryController::class, 'accept'])->name('deliveries.accept');
        Route::post('/deliveries/{order}/decline', [DeliveryController::class, 'decline'])->name('deliveries.decline');
        Route::post('/deliveries/{order}/pickup', [DeliveryController::class, 'pickup'])->name('deliveries.pickup');
        Route::post('/deliveries/{order}/deliver', [DeliveryController::class, 'deliver'])->name('deliveries.deliver');
        Route::get('/location', [LocationController::class, 'show'])->name('location.show');
        Route::put('/location', [LocationController::class, 'update'])->name('location.update');
        Route::get('/earnings', [EarningsController::class, 'index'])->name('earnings');
        Route::get('/account', [RiderAccountController::class, 'show'])->name('account');
        Route::put('/account', [RiderAccountController::class, 'update'])->name('account.update');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::patch('/users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('users.update-status');
        Route::get('/approvals', [AdminApprovalController::class, 'index'])->name('approvals');
        Route::post('/approvals/stores/{store}/approve', [AdminApprovalController::class, 'approveStore'])->name('approvals.stores.approve');
        Route::post('/approvals/stores/{store}/reject', [AdminApprovalController::class, 'rejectStore'])->name('approvals.stores.reject');
        Route::post('/approvals/stores/{store}/suspend', [AdminApprovalController::class, 'suspendStore'])->name('approvals.stores.suspend');
        Route::post('/approvals/riders/{rider}/approve', [AdminApprovalController::class, 'approveRider'])->name('approvals.riders.approve');
        Route::post('/approvals/riders/{rider}/reject', [AdminApprovalController::class, 'rejectRider'])->name('approvals.riders.reject');
        Route::post('/approvals/riders/{rider}/suspend', [AdminApprovalController::class, 'suspendRider'])->name('approvals.riders.suspend');
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/assign-rider', [AdminOrderController::class, 'assignRider'])->name('orders.assign-rider');
        Route::resource('categories', AdminCategoryController::class)->except(['show']);
        Route::get('/psgc', [AdminPsgcController::class, 'index'])->name('psgc.index');
        Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');
        Route::get('/voc', [AdminVocController::class, 'index'])->name('voc.index');
        Route::post('/voc/extract', [AdminVocController::class, 'extract'])->name('voc.extract');
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');
        Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::get('/map', [AdminMapController::class, 'index'])->name('map.index');
    });
});
