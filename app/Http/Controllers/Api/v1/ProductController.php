<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\StoreReviewRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\v1\Product\ProductListResource;
use App\Http\Resources\v1\Product\ProductResource;
use App\Http\Resources\v1\Product\ProductReviewResource;
use App\Models\Product;
use App\Services\Product\ProductService;
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

    public function index(ProductService $productService)
    {
        return ProductListResource::collection($productService->published());
    }

    public function show(Product $product)
    {
        return ProductResource::make($product);
    }

    public function store(StoreProductRequest $request, ProductService $productService)
    {
        $product = $productService->store($request);

        return ProductResource::make($product);
    }

    public function update(Product $product, UpdateProductRequest $request, ProductService $productService)
    {
        $product = $productService
            ->setProduct($product)
            ->update($request);

        return ProductResource::make($product);
    }

    public function addReview(Product $product, StoreReviewRequest $request, ProductService $productService)
    {
        $review = $productService
            ->setProduct($product)
            ->addReview($request);

        return ProductReviewResource::make($review);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return responseOk();
    }
}
