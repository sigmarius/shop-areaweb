<?php

declare(strict_types=1);

namespace App\Services\Post;

use App\DTOs\Post\StorePostDTO;
use App\DTOs\Post\UpdatePostDTO;
use App\Models\Post;

class PostService
{
    private Post $post;

    public function setPost(Post $post): PostService
    {
        $this->post = $post;

        return $this;
    }

    public function store(StorePostDTO $data): Post
    {
        return auth()->user()->posts()->create([
            'photo' => uploadImage($data->photo, 'posts'),
            'description' => $data->description,
        ]);
    }

    public function update(UpdatePostDTO $data): Post
    {
        $this->post->update($data->toArray());

        return $this->post;
    }
}
