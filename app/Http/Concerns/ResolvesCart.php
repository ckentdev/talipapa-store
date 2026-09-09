<?php

namespace App\Http\Concerns;

use App\Models\Cart;
use App\Services\CartService;

trait ResolvesCart
{
    protected function resolveCart(): Cart
    {
        $cartService = app(CartService::class);

        if (auth()->check()) {
            return $cartService->getOrCreateCart(auth()->user());
        }

        return $cartService->getOrCreateCart(null, session()->getId());
    }
}
