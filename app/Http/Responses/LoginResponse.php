<?php

namespace App\Http\Responses;

use App\Enums\UserRole;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();
        $route = $user?->role?->dashboardRoute() ?? 'customer.home';

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 200)
            : redirect()->intended(route($route));
    }
}
