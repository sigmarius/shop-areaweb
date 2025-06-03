<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use App\DTOs\Post\StorePostDTO;
use App\Http\Requests\ApiRequest;

class StorePostRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:1000'],
            'description' => ['nullable', 'max:255'],
        ];
    }

    public function toDTO(): StorePostDTO
    {
        return StorePostDTO::from([
            'photo' => $this->file('photo'),
            'description' => $this->input('description'),
        ]);
    }
}
