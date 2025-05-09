<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatusEnum;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\StoreReviewRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function __construct()
    {
        // TODO: имитация, реализовать после авторизации
        // авторизует на текущую сессию любого пользователя-администратора
        auth()->login(User::query()->inRandomOrder()->where('is_admin', true)->first());
    }

    public function index()
    {
        $products = Product::query()
            ->select(['id', 'name', 'price'])
            ->where('status', ProductStatusEnum::Published)
            ->get();

        return $products->map(fn(Product $product) => [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'rating' => $product->rating()
        ]);
    }

    public function show(Product $product)
    {
        if ($product->status == ProductStatusEnum::Draft) {
            return response()->json([
                'message' => 'Product not found'
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'rating' => $product->rating(),
            'images' => $product->images->map(fn(ProductImage $image) => $image->url),
            'count' => $product->count,
            'reviews' => $product->reviews->map(fn(ProductReview $review) => [
                'id' => $review->id,
                'userName' => $review->user->name,
                'text' => $review->text,
                'rating' => $review->rating
            ])
        ];
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

    public function review(Product $product, StoreReviewRequest $request)
    {
        return $product->reviews()->create([
            'user_id' => auth()->id(),
            'text' => $request->str('text'),
            'rating' => $request->integer('rating'),
        ])->only('id');
    }
}
