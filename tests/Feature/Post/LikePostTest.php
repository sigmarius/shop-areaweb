<?php

declare(strict_types=1);

namespace Tests\Feature\Post;

use App\Enums\LikeState;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LikePostTest extends TestCase
{
    use RefreshDatabase;

    private Post $likedPost;
    private Post $unlikedPost;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn(false);

        $this->likedPost = Post::factory()->create([
            'user_id' => $this->getCurrentUserId()
        ]);

        $this->likedPost->likes()->create([
            'user_id' => $this->getCurrentUserId()
        ]);

        $this->unlikedPost = Post::factory()->create([
            'user_id' => $this->getCurrentUserId()
        ]);
    }

    #[Test]
    public function like_action_to_post_unliked_successfully(): void
    {
        $response = $this->postJson(route('api.v1.posts.like', [
            'post' => $this->unlikedPost->id,
        ]));

        $response->assertOk();

        $response->assertJsonStructure([
            'state',
        ]);

        $this->assertEquals(LikeState::Liked->value, $response->json('state'));
    }

    #[Test]
    public function unlike_action_to_post_liked_successfully(): void
    {
        $response = $this->postJson(route('api.v1.posts.like', [
            'post' => $this->likedPost->id,
        ]));

        $response->assertOk();

        $response->assertJsonStructure([
            'state',
        ]);

        $this->assertEquals(LikeState::Unliked->value, $response->json('state'));
    }
}
