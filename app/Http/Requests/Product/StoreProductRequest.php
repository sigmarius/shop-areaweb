<?php

namespace App\Http\Requests\Product;

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
            'status' =>['required', new Enum(ProductStatusEnum::class)],
            'images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
