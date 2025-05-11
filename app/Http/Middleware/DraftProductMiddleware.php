<?php

namespace App\Http\Middleware;

use App\Enums\ProductStatusEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DraftProductMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // достаем продукт из Route Binding
        $product = $request->route("product");

        if ($product->isDraft()) {
            return response()->json([
                'message' => 'Product not found'
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}
