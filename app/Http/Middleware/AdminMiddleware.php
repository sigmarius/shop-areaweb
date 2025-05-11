<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->user()->is_admin) {
            return response()->json([
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
