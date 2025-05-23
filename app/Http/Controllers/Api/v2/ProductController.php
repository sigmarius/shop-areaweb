<?php

namespace App\Http\Controllers\Api\v2;

use App\Facades\ProductFacade;
use App\Http\Controllers\Controller;
use App\Http\Resources\v2\Product\ProductListResource;
use App\Services\Product\ProductService;
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
        return ProductListResource::collection(
            // после привязки сервиса к фасаду в AppServiceProvider
            // все методы сервисного класса можно вызывать как статические,
            // не делая инъекций сервисного класса в параметрах метода
            ProductFacade::published()
        );
    }
}
