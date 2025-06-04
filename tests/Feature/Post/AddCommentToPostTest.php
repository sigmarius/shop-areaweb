<?php

declare(strict_types=1);

namespace Tests\Feature\Post;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddCommentToPostTest extends TestCase
{
    use RefreshDatabase;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn(false);

        $this->post = Post::factory()->create([
            'user_id' => $this->getCurrentUserId()
        ]);
    }

    #[Test]
    public function comment_added_to_post_successfully(): void
    {
        $data = [
            'comment' => fake()->sentence
        ];

        $response = $this->postJson(route('api.v1.posts.comment', [
            'post' => $this->post->id,
        ]), $data);

        $response->assertCreated();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'user' => [
                    'id',
                    'name',
                    'avatar',
                ],
                'comment',
                'created_at',
            ]
        ]);

        $response->assertJson([
            'data' => [
                'id' => $this->post->id,
                'user' => [
                    'id' => $this->post->user->id,
                    'name' => $this->post->user->name,
                    'avatar' => $this->post->user->avatar,
                ],
                'comment' => Arr::get($data, 'comment'),
            ]
        ]);

        $this->assertDatabaseHas(Comment::class, [
            'id' => $response->json('data.id'),
            'post_id' => $this->post->id,
            'user_id' => $this->getCurrentUserId(),
            'comment' => Arr::get($data, 'comment'),
        ]);
    }
}
