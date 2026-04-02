<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Enums\StatusEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveUserMiddleware
{
    /**
     * Block inactive users even if they have a valid Sanctum token.
     *
     * Why this matters: When an admin deactivates a user, that user's existing
     * tokens are NOT automatically revoked. Without this middleware, a deactivated
     * user could continue using the API until their token expires.
     *
     * This middleware must run AFTER auth:sanctum but BEFORE role checks.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status === StatusEnum::Inactive) {
            return ApiResponse::error(
                'Your account has been deactivated. Please contact an administrator.',
                403
            );
        }

        return $next($request);
    }
}