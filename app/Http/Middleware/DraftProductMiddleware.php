<?php

namespace App\Http\Middleware;

use App\Exceptions\Product\ProductNotFoundException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DraftProductMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     * @throws ProductNotFoundException
     */
    public function handle(Request $request, Closure $next): Response
    {
        // достаем продукт из Route Binding
        $product = $request->route("product");

        if ($product->isDraft()) {
            // это исключение будет обрабатываться в bootstrap/app.php
            // за счет добавленных в него методов report() и render()
            // можно не делать try/catch для каждого метода отдельно
            throw new ProductNotFoundException();
        }

        return $next($request);
    }
}
