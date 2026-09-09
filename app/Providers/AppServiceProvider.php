<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use App\Http\Responses\RegisterResponse;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);
    }

    public function boot(): void
    {
        RateLimiter::for('voice-assistant', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(40)->by('voice:'.$request->user()->id)
                : Limit::perMinute(20)->by('voice:'.$request->ip());
        });

        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);
    }
}
