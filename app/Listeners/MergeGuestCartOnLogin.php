<?php

namespace App\Listeners;

use App\Services\CartService;
use Illuminate\Auth\Events\Login;

class MergeGuestCartOnLogin
{
    public function __construct(
        private readonly CartService $cartService,
    ) {}

    public function handle(Login $event): void
    {
        $sessionId = session()->getId();

        if ($sessionId) {
            $this->cartService->mergeGuestCart($sessionId, $event->user);
        }
    }
}
