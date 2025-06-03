<?php

declare(strict_types=1);

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyPostTest extends TestCase
{
    use RefreshDatabase;

    private Post $post;
    private Post $somebodyPost;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn(false);

        $this->post = Post::factory()->create([
            'user_id' => $this->getCurrentUserId()
        ]);

        $this->somebodyPost = Post::factory()
            ->for(User::factory())
            ->create();
    }

    #[Test]
    public function owner_post_deleted_successfully(): void
    {
        $response = $this->deleteJson(route('api.v1.posts.destroy', [
            'post' => $this->post
        ]));

        $response->assertNoContent();

        $this->assertDatabaseMissing(Post::class, [
            'id' => $this->post->id,
        ]);
    }

    #[Test]
    public function somebody_post_deleted_forbidden(): void
    {
        $response = $this->deleteJson(route('api.v1.posts.destroy', [
            'post' => $this->somebodyPost
        ]));

        $response->assertForbidden();

        $response->assertJsonStructure(['message']);

        $this->assertDatabaseHas(Post::class, [
            'id' => $this->somebodyPost->id,
        ]);
    }
}
