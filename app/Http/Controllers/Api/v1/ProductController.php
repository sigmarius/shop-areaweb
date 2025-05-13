<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\ProductStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\StoreReviewRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\v1\Product\ProductListResource;
use App\Http\Resources\v1\Product\ProductResource;
use App\Models\Product;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

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
        $products = Product::query()
            ->select(['id', 'name', 'price'])
            ->where('status', ProductStatusEnum::Published)
            ->get();

        return ProductListResource::collection($products);
    }

    public function show(Product $product)
    {
        return ProductResource::make($product);
    }

    public function store(StoreProductRequest $request)
    {
        $product = auth()->user()->products()->create([
            'name' => $request->str('name'),
            'description' => $request->str('description'),
            'price' => $request->input('price'),
            'count' => $request->integer('count'),
            'status' => $request->enum('status', ProductStatusEnum::class),
        ]);

        foreach ($request->file('images') as $image) {
            // сохраняем картинку в публичном доступе
            $path = $image->storePublicly('images');

            $product->images()->create([
                'url' => Storage::url($path),
            ]);
        }

        return response()->json([
            'message' => 'Product created',
            'id' => $product->id
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(Product $product, UpdateProductRequest $request)
    {
        if ($request->method() === 'PUT') {
            $product->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'count' => $request->input('count', null),
                'status' => $request->enum('status', ProductStatusEnum::class),
            ]);
        } else {
            // TODO использовать DTO
            $product->update($request->validated());
        }
    }

    public function addReview(Product $product, StoreReviewRequest $request)
    {
        return $product->reviews()->create([
            'user_id' => auth()->id(),
            'text' => $request->str('text'),
            'rating' => $request->integer('rating'),
        ])->only('id');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(status:  Response::HTTP_NO_CONTENT);
    }
}
