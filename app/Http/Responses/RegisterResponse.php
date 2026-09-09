<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): Response
    {
        $route = $request->user()?->role?->dashboardRoute() ?? 'landing';

        return $request->wantsJson()
            ? new JsonResponse('', 201)
            : redirect()->intended(route($route))
                ->with('success', 'Welcome to Talipapa! Your account is ready.');
    }
}
