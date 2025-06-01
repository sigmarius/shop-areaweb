<?php

declare(strict_types=1);

namespace Tests\Feature\Users;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetUserTest extends TestCase
{
    use RefreshDatabase;

    // пользователь, на которого будет подписываться авторизованный пользователь
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();

        $this->user = User::factory()->create();

        // авторизованный пользователь подписывается на тестового пользователя
        Subscription::query()->create([
            'user_id' => $this->user->id,
            'subscriber_id' => $this->getCurrentUserId()
        ]);
    }

    #[Test]
    public function get_user_successfully(): void
    {
        $response = $this->getJson(route('api.v1.users.get-user', [
            'user' => $this->user->id
        ]));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'subscribers',
                'publications',
                'avatar',
                'about',
                'isVerified',
                'registeredAt',
                'isSubscribed'
            ],
        ]);

        // проверяем что авторизованный пользователь подписан на тестового пользователя
        $this->assertTrue($response->json('data.isSubscribed'));
    }

    #[Test]
    public function user_not_found(): void
    {
        $response = $this->getJson(route('api.v1.users.get-user', [
            'user' => 0 // такого пользователя не существует
        ]));

        $response->assertNotFound();

        $response->assertJsonStructure([
            'status',
            'message',
            'error'
        ]);

        $response->assertJson([
            'status' => 'error',
            'message' => __('errors.default_core.model_not_found', ['model' => 'User'])
        ]);
    }
}
