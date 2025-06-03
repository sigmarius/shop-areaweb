<?php

declare(strict_types=1);

namespace Tests\Feature\Post;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreatePostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(bool $isAdmin = true): void
    {
        parent::setUp();

        $this->signIn(false);
    }

    #[Test]
    public function post_created_successfully(): void
    {
        $data = [
            'photo' => UploadedFile::fake()->image('photo.jpg'),
            'description' => fake()->sentence
        ];

        $response = $this->postJson(route('api.v1.posts.store'), $data);

        $response->assertCreated();

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

        $response->assertJson([
            'data' => [
                'description' => Arr::get($data, 'description'),
                'likes' => 0,
                'isLiked' => false,
                'comments' => [
                    'total' => 0,
                    'list' => [],
                ],
            ]
        ]);

        $this->assertDatabaseHas(Post::class, [
            'id' => $response->json('data.id'),
            'description' => $response->json('data.description'),
            'photo' => $response->json('data.photo'),
        ]);
    }

    #[Test]
    public function validation_required_successfully()
    {
        $data = [
            'photo' => null,
            'description' => null
        ];

        $response = $this->postJson(route('api.v1.posts.store'), $data);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['photo']);
    }

    #[Test]
    public function validation_photo_type_successfully()
    {
        $data = [
            'photo' => UploadedFile::fake()->image('photo.gif'),
            'description' => fake()->sentence
        ];

        $response = $this->postJson(route('api.v1.posts.store'), $data);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['photo']);
    }
}
