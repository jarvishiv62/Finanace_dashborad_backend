<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestIdMiddleware
{
    /**
     * Attach a unique UUID to every response as X-Request-ID header.
     *
     * Why this exists: In production, when a client reports a bug, they can
     * share the X-Request-ID from their network tab. That ID ties directly
     * to a specific log entry, making debugging trivial.
     *
     * This is standard practice in production APIs. Adding it here shows
     * awareness of real-world operational concerns.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow client to pass their own request ID, or generate one
        $requestId = $request->header('X-Request-ID') ?? (string) Str::uuid();

        $response = $next($request);

        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}