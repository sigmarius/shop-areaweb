<?php

namespace App\Http\Controllers\Api\v2;

use App\Enums\ProductStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\v2\Product\ProductListResource;
use App\Models\Product;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            // используется аутентификация от sanctum
            new Middleware('auth:sanctum', only: ['index']),
        ];
    }

    public function index()
    {
        $products = Product::query()
            ->select(['id', 'name', 'price'])
            ->where('status', ProductStatusEnum::Published)
            ->get();

        return ProductListResource::collection($products);
    }
}
