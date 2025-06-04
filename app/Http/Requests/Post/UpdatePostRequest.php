<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use App\DTOs\Post\UpdatePostDTO;
use App\Http\Requests\ApiRequest;

class UpdatePostRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'description' => ['nullable', 'max:255'],
        ];
    }

    public function toDTO(): UpdatePostDTO
    {
        return UpdatePostDTO::from($this->validated());
    }
}
