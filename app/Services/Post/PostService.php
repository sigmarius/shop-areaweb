<?php

declare(strict_types=1);

namespace App\Services\Post;

use App\DTOs\Post\StorePostDTO;
use App\Models\Post;

class PostService
{
    public function store(StorePostDTO $data): Post
    {
        return auth()->user()->posts()->create([
            'photo' => uploadImage($data->photo, 'posts'),
            'description' => $data->description,
        ]);
    }
}
