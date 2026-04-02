<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:
     *   middleware('role:admin')
     *   middleware('role:admin,analyst')
     *
     * Important: This middleware always runs AFTER auth:sanctum.
     * Never check roles on an unauthenticated request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Auth guard should have already rejected unauthenticated requests,
        // but we double-check here for safety
        if (!$user) {
            return ApiResponse::error('Unauthenticated.', 401);
        }

        // Check if the user's role is in the allowed roles list
        if (!in_array($user->role->value, $roles)) {
            return ApiResponse::error(
                'You do not have permission to perform this action.',
                403
            );
        }

        return $next($request);
    }
}