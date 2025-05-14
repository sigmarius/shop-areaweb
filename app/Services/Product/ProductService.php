<?php

namespace App\Services\Product;

use App\Enums\ProductStatusEnum;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\StoreReviewRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    private Product $product;

    /**
     * Через fluent интерфейс пробрасывает продукт в класс, и возвращает сущность класса
     * используется в контроллерах
     * @see ProductController::update()
     *
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product): ProductService
    {
        $this->product = $product;
        return $this;
    }

    public function published(array $fields = ['id', 'name', 'price']): Collection
    {
        return Product::query()
            ->select($fields)
            ->where('status', ProductStatusEnum::Published)
            ->get();
    }

    public function store(StoreProductRequest $request): Product
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

        return $product;
    }

    public function update(UpdateProductRequest $request): Product
    {
        if ($request->method() === 'PUT') {
            $this->product->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'count' => $request->input('count', null),
                'status' => $request->enum('status', ProductStatusEnum::class),
            ]);
        } else {
            // TODO использовать DTO
            $this->product->update($request->validated());
        }

        return $this->product;
    }

    public function addReview(StoreReviewRequest $request): ProductReview
    {
        return $this->product->reviews()->create([
            'user_id' => auth()->id(),
            'text' => $request->str('text'),
            'rating' => $request->integer('rating'),
        ]);
    }
}
