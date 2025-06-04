<?php

declare(strict_types=1);

namespace App\Services\Post;

use App\DTOs\Post\StorePostDTO;
use App\DTOs\Post\UpdatePostDTO;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * Лента постов - возвращаются только посты пользователей,
     * на которых подписан авторизованный пользователь
     *
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public function feed(int $limit = 10, int $offset = 0): Collection
    {
        return auth()->user()
            ->feedPosts()
            ->limit($limit)
            ->offset($offset)
            ->orderByDesc('created_at')
            ->get();
    }

    public function totalFeedPosts(): ?int
    {
        return auth()->user()
            ->feedPosts()
            ->count();
    }
}
