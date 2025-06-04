<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdatePostTest extends TestCase
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
    public function post_updated_successfully(): void
    {
        $data = [
            'description' => fake()->sentence
        ];

        $response = $this->patchJson(route('api.v1.posts.update', [
            'post' => $this->post
        ]), $data);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'photo',
                'user' => [
                    'id',
                    'name',
                    'avatar',
                ],
                'description',
                'likes',
                'isLiked',
                'comments' => [
                    'total',
                    'list' => []
                ],
                'createdAt',
            ]
        ]);

        $this->assertDatabaseHas(Post::class, [
            'id' => $this->post->id,
            'description' => $response->json('data.description'),
        ]);
    }
}
