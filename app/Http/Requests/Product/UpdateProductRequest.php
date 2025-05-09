<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductStatusEnum;
use App\Http\Requests\ApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

class UpdateProductRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:1'],
            'count' => ['nullable', 'integer', 'min:0'],
            'status' =>['nullable', new Enum(ProductStatusEnum::class)],
        ];
    }
}
