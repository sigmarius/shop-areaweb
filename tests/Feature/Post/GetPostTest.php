<?php

declare(strict_types=1);

namespace Tests\Feature\Post;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetPostTest extends TestCase
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
    public function get_post_successfully(): void
    {
        $response = $this->getJson(route('api.v1.posts.show', [
            'post' => $this->post
        ]));

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
    }
}
