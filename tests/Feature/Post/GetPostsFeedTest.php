<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetPostsFeedTest extends TestCase
{
    use RefreshDatabase;

    private User $subscribedUser1;
    private User $subscribedUser2;
    private User $notSubscribedUser;

    private string $defaultRoute;

    protected function setUp(): void
    {
        parent::setUp();

        // задаем маршрут по умолчанию с дефолтными параметрами
        $this->defaultRoute = route('api.v1.posts.index', [
            'limit' => 10,
            'offset' => 0,
        ]);

        $this->createCurrentUser(false);

        // Создаем пользователей, на которых подписан currentUser
        $this->subscribedUser1 = User::factory()->create();
        $this->subscribedUser2 = User::factory()->create();

        // Создаем пользователя, на которого НЕ подписан currentUser
        $this->notSubscribedUser = User::factory()->create();

        // Создаем подписки
        Subscription::factory()->create([
            'subscriber_id' => $this->getCurrentUserId(),
            'user_id' => $this->subscribedUser1->id,
        ]);

        Subscription::factory()->create([
            'subscriber_id' => $this->getCurrentUserId(),
            'user_id' => $this->subscribedUser2->id,
        ]);

        // Создаем посты для каждого пользователя с разными датами
        Post::factory()->count(3)->sequence(
            ['created_at' => now()->subDays(3)],
            ['created_at' => now()->subDays(2)],
            ['created_at' => now()->subDays(1)]
        )->create(['user_id' => $this->subscribedUser1->id]);

        Post::factory()->count(2)->sequence(
            ['created_at' => now()->subHours(3)],
            ['created_at' => now()->subHours(1)]
        )->create(['user_id' => $this->subscribedUser2->id]);

        Post::factory()->count(4)
            ->create(['user_id' => $this->notSubscribedUser->id]);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $response = $this->getJson(route('api.v1.posts.index'));

        $response->assertUnauthorized();
    }

    #[Test]
    public function it_returns_only_posts_from_subscribed_users(): void
    {
        Sanctum::actingAs($this->getCurrentUser());

        $response = $this->getJson($this->defaultRoute);

        $response->assertOk();

        // Проверяем структуру ответа
        $response->assertJsonStructure([
            'total',
            'posts' => [
                '*' => [
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
                    'comments',
                    'createdAt',
                ]
            ]
        ]);

        // Проверяем, что вернулись только посты подписанных пользователей
        $posts = $response->json('posts');
        $this->assertCount(5, $posts);

        // Проверяем, что нет постов от неподписанного пользователя
        $userIds = collect($posts)
            ->pluck('user.id')
            ->unique();

        $this->assertContains($this->subscribedUser1->id, $userIds);
        $this->assertContains($this->subscribedUser2->id, $userIds);
        $this->assertNotContains($this->notSubscribedUser->id, $userIds);
    }

    #[Test]
    public function it_returns_correct_total_count(): void
    {
        Sanctum::actingAs($this->getCurrentUser());

        $response = $this->getJson($this->defaultRoute);

        $response->assertOk();

        $this->assertEquals(5, $response->json('total'));
    }

    #[Test]
    public function it_respects_limit_parameter(): void
    {
        Sanctum::actingAs($this->getCurrentUser());

        $response = $this->getJson(route('api.v1.posts.index', [
            'limit' => 2,
            'offset' => 0,
        ]));

        $response->assertOk();
        $this->assertCount(2, $response->json('posts'));
        $this->assertEquals(5, $response->json('total')); // total должен оставаться полным количеством
    }

    #[Test]
    public function it_respects_offset_parameter(): void
    {
        Sanctum::actingAs($this->getCurrentUser());

        // Получаем все посты для сравнения
        $allPosts = $this->getCurrentUser()
            ->feedPosts()
            ->orderByDesc('created_at')
            ->get();

        // Запрашиваем со смещением 2
        $response = $this->getJson(route('api.v1.posts.index', [
            'limit' => 10,
            'offset' => 2
        ]));

        $posts = $response->json('posts');

        $response->assertOk();
        $this->assertCount(3, $posts);
        $this->assertEquals(5, $response->json('total'));

        // Проверяем, что вернулись правильные посты (пропущены первые 2)
        $this->assertEquals($allPosts[2]->id, $posts[0]['id']);
        $this->assertEquals($allPosts[3]->id, $posts[1]['id']);
        $this->assertEquals($allPosts[4]->id, $posts[2]['id']);
    }

    #[Test]
    public function it_returns_posts_in_correct_order(): void
    {
        Sanctum::actingAs($this->getCurrentUser());

        // Получаем посты напрямую через связь для сравнения
        $expectedPosts = $this->getCurrentUser()->feedPosts()
            ->orderByDesc('created_at')
            ->get();

        $response = $this->getJson($this->defaultRoute);
        $actualPosts = $response->json('posts');

        $response->assertOk();

        // Проверяем порядок постов (сначала новые)
        for ($i = 0; $i < count($expectedPosts); $i++) {
            $this->assertEquals($expectedPosts[$i]->id, $actualPosts[$i]['id']);
        }

        // Проверяем, что самый новый пост первый
        /** @var Post $newestPost */
        $newestPost = $expectedPosts
            ->sortByDesc('created_at')
            ->first();

        $this->assertEquals($newestPost->id, $actualPosts[0]['id']);
    }

    #[Test]
    public function it_returns_empty_array_when_no_subscriptions(): void
    {
        $newUser = User::factory()->create();
        Sanctum::actingAs($newUser);

        $response = $this->getJson($this->defaultRoute);

        $response->assertOk();
        $this->assertCount(0, $response->json('posts'));
        $this->assertEquals(0, $response->json('total'));

        // Проверяем структуру даже при пустом результате
        $response->assertJsonStructure([
            'total',
            'posts',
        ]);
    }

    #[Test]
    public function it_returns_correct_post_resource_fields(): void
    {
        Sanctum::actingAs($this->getCurrentUser());

        // Берем первый пост для детальной проверки
        /** @var Post $post */
        $post = $this->getCurrentUser()->feedPosts()
            ->orderByDesc('created_at')
            ->first();

        $response = $this->getJson($this->defaultRoute);
        $firstPost = $response->json('posts.0');

        $response->assertOk();

        // Проверяем все поля ресурса
        $this->assertEquals($post->id, $firstPost['id']);
        $this->assertEquals($post->photo, $firstPost['photo']);
        $this->assertEquals($post->description, $firstPost['description']);
        $this->assertEquals($post->totalLikes(), $firstPost['likes']);
        $this->assertEquals($post->isLiked(), $firstPost['isLiked']);
        $this->assertEquals($post->totalComments(), $firstPost['comments']);
        $this->assertEquals($post->created_at->format('d/m/Y H:i'), $firstPost['createdAt']);

        // Проверяем структуру пользователя в посте
        $this->assertArrayHasKey('user', $firstPost);
        $this->assertEquals($post->user->id, $firstPost['user']['id']);
        $this->assertEquals($post->user->name, $firstPost['user']['name']);
        $this->assertEquals($post->user->avatar, $firstPost['user']['avatar']);
    }

    #[Test]
    public function it_handles_invalid_limit_and_offset_parameters(): void
    {
        Sanctum::actingAs($this->getCurrentUser());

        // Отрицательные значения
        $response = $this->getJson(route('api.v1.posts.index', [
            'limit' => -1,
            'offset' => -1
        ]));
        $response->assertUnprocessable();

        // Строковые значения
        $response = $this->getJson(route('api.v1.posts.index', [
            'limit' => 'abc',
            'offset' => 'def'
        ]));

        $response->assertUnprocessable();

        // Слишком большие значения
        $response = $this->getJson(route('api.v1.posts.index', [
            'limit' => 1000,
            'offset' => 1000
        ]));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['limit']);

        // Проверяем, что с default значениями работает
        $response = $this->getJson($this->defaultRoute);
        $response->assertOk();
        $this->assertCount(5, $response->json('posts'));
    }
}
