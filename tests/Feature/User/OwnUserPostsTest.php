<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OwnUserPostsTest extends TestCase
{
    use RefreshDatabase;

    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn(false);

        // Создаем тестового пользователя
        $this->otherUser = User::factory()->create();

        // Создаем посты с разными датами для тестового пользователя
        Post::factory()->count(5)->sequence(
            ['created_at' => now()->subDays(4)],
            ['created_at' => now()->subDays(3)],
            ['created_at' => now()->subDays(2)],
            ['created_at' => now()->subDays(1)],
            ['created_at' => now()]
        )->create(['user_id' => $this->getCurrentUser()]);

        // Создаем посты для другого пользователя (не должны отображаться)
        Post::factory()->count(3)->create([
            'user_id' => $this->otherUser->id
        ]);
    }

    #[Test]
    public function it_returns_posts_for_specified_user(): void
    {
        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $this->getCurrentUser(),
            'limit' => 10,
            'offset' => 0,
        ]));

        $response->assertOk()
            ->assertJsonStructure([
                'posts' => [
                    '*' => [
                        'id',
                        'photo',
                        'user' => ['id', 'name', 'avatar'],
                        'description',
                        'likes',
                        'isLiked',
                        'comments',
                        'createdAt',
                    ]
                ],
                'total'
            ]);

        // Проверяем что вернулись только посты запрошенного пользователя
        $posts = $response->json('posts');
        $this->assertCount(5, $posts);

        $userIds = collect($posts)
            ->pluck('user.id')
            ->unique();

        $this->assertCount(1, $userIds);
        $this->assertEquals($this->getCurrentUserId(), $userIds->first());
    }

    #[Test]
    public function it_returns_correct_total_count(): void
    {
        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $this->getCurrentUser(),
            'limit' => 10,
            'offset' => 0,
        ]));

        $response->assertOk();
        $this->assertEquals(5, $response->json('total'));
    }

    #[Test]
    public function it_returns_posts_in_descending_order(): void
    {
        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $this->getCurrentUser(),
            'limit' => 10,
            'offset' => 0,
        ]));

        $posts = $response->json('posts');

        // Проверяем порядок по убыванию даты
        for ($i = 0; $i < count($posts) - 1; $i++) {
            $currentDate = strtotime($posts[$i]['createdAt']);
            $nextDate = strtotime($posts[$i + 1]['createdAt']);
            $this->assertGreaterThanOrEqual($nextDate, $currentDate);
        }
    }

    #[Test]
    public function it_respects_limit_parameter(): void
    {
        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $this->getCurrentUserId(),
            'limit' => 2,
            'offset' => 0,
        ]));

        $response->assertOk();

        $this->assertCount(2, $response->json('posts'));
        $this->assertEquals(5, $response->json('total'));
    }

    #[Test]
    public function it_respects_offset_parameter(): void
    {
        $allPosts = $this->getCurrentUser()->posts()
            ->orderByDesc('created_at')
            ->get();

        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $this->getCurrentUser(),
            'limit' => 10,
            'offset' => 2,
        ]));

        $posts = $response->json('posts');

        $response->assertOk();
        $this->assertCount(3, $posts);
        $this->assertEquals(5, $response->json('total'));

        // Проверяем что пропущены первые 2 поста
        $this->assertEquals($allPosts[2]->id, $posts[0]['id']);
        $this->assertEquals($allPosts[3]->id, $posts[1]['id']);
        $this->assertEquals($allPosts[4]->id, $posts[2]['id']);
    }

    #[Test]
    public function it_returns_empty_array_for_user_without_posts(): void
    {
        $newUser = User::factory()->create();

        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $newUser,
            'limit' => 10,
            'offset' => 0,
        ]));

        $response->assertOk()
            ->assertJson([
                'posts' => [],
                'total' => 0
            ]);
    }

    #[Test]
    public function it_returns_404_for_non_existent_user(): void
    {
        $nonExistentId = 9999;

        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $nonExistentId,
            'limit' => 10,
            'offset' => 0,
        ]));

        $response->assertNotFound();
    }

    #[Test]
    public function it_validates_request_parameters(): void
    {
        // Невалидный limit (меньше 1)
        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $this->getCurrentUser(),
            'limit' => 0,
            'offset' => 0,
        ]));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['limit']);

        // Невалидный limit (больше 100)
        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $this->getCurrentUser(),
            'limit' => 101,
            'offset' => 0,
        ]));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['limit']);

        // Невалидный offset (отрицательный)
        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $this->getCurrentUser(),
            'limit' => 10,
            'offset' => -1,
        ]));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['offset']);

        // Отсутствует limit
        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $this->getCurrentUser(),
            'offset' => 0,
        ]));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['limit']);

        // Отсутствует offset
        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $this->getCurrentUser(),
            'limit' => 10,
        ]));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['offset']);
    }

    #[Test]
    public function it_returns_correct_post_resource_data(): void
    {
        $post = $this->getCurrentUser()
            ->posts()
            ->latest()
            ->first();

        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $this->getCurrentUser(),
            'limit' => 10,
            'offset' => 0,
        ]));

        $responsePost = $response->json('posts.0');

        $response->assertOk();

        // Проверяем все поля ресурса
        $this->assertEquals($post->id, Arr::get($responsePost, 'id'));
        $this->assertEquals($post->photo, Arr::get($responsePost, 'photo'));
        $this->assertEquals($post->description, Arr::get($responsePost, 'description'));
        $this->assertEquals($post->totalLikes(), Arr::get($responsePost, 'likes'));
        $this->assertEquals($post->isLiked(), Arr::get($responsePost, 'isLiked'));
        $this->assertEquals($post->totalComments(), Arr::get($responsePost, 'comments'));
        $this->assertEquals($post->created_at->format('d/m/Y H:i'), Arr::get($responsePost, 'createdAt'));

        // Проверяем данные пользователя
        $this->assertEquals($post->user->id, Arr::get($responsePost, 'user.id'));
        $this->assertEquals($post->user->name, Arr::get($responsePost, 'user.name'));
        $this->assertEquals($post->user->avatar, Arr::get($responsePost, 'user.avatar'));
    }

    #[Test]
    public function it_returns_correct_data_when_limit_exceeds_total(): void
    {
        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $this->getCurrentUser(),
            'limit' => 100,
            'offset' => 0,
        ]));

        $response->assertOk();

        $this->assertCount(5, $response->json('posts'));
        $this->assertEquals(5, $response->json('total'));
    }

    #[Test]
    public function it_returns_empty_array_when_offset_exceeds_total(): void
    {
        $response = $this->getJson(route('api.v1.users.posts', [
            'user' => $this->getCurrentUser(),
            'limit' => 10,
            'offset' => 10,
        ]));

        $response->assertOk();

        $this->assertCount(0, $response->json('posts'));
        $this->assertEquals(5, $response->json('total'));
    }
}
