<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Facades\ProductFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\StoreReviewRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\v1\Product\ProductListResource;
use App\Http\Resources\v1\Product\ProductResource;
use App\Http\Resources\v1\Product\ProductReviewResource;
use App\Models\Product;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            // используется аутентификация от sanctum
            new Middleware('auth:sanctum', except: ['index', 'show']),

            // проверка доступа - только для администраторов
            new Middleware('admin', only: ['store', 'update', 'destroy']),

            // проверка статуса продукта - просматривать можно только опубликованные
            new Middleware('product.draft', only: ['show']),
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

    public function show(Product $product)
    {
        return ProductResource::make(
            $product->load(['reviews', 'reviews.user'])
        );
    }

    public function store(StoreProductRequest $request)
    {
        // преобразуем реквест в DTO и передаем в сервис
        $product = ProductFacade::store($request->dto());

        return ProductResource::make($product);
    }

    public function update(Product $product, UpdateProductRequest $request)
    {
        $product = ProductFacade::setProduct($product)
            ->update($request);

        return ProductResource::make($product);
    }

    public function addReview(Product $product, StoreReviewRequest $request)
    {
        $review = ProductFacade::setProduct($product)
            ->addReview($request);

        return ProductReviewResource::make($review);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return responseOk();
    }
}
