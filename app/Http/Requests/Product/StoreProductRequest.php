<?php

namespace App\Http\Requests\Product;

use App\DTOs\CreateProductDTO;
use App\Enums\ProductStatusEnum;
use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rules\Enum;

class StoreProductRequest extends ApiRequest
{    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['string'],
            'price' => ['required', 'numeric', 'min:1'],
            'count' => ['required', 'integer', 'min:0'],

            // Пример - в БД поле называется 'status', а в реквесте - 'state'
            'state' =>['required', new Enum(ProductStatusEnum::class)],

            'images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    /**
     * Преобразуем провалидированный реквест в DTO
     *
     * @return CreateProductDTO
     *
     */
    public function dto(): CreateProductDTO
    {
        return CreateProductDTO::from($this->validated());
    }
}
