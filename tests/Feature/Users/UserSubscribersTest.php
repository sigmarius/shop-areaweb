<?php

declare(strict_types=1);

namespace Tests\Feature\Users;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserSubscribersTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn(false);

        $this->user = User::factory()
            ->has(
                Subscription::factory(5)
                    ->for(User::factory(), 'subscriber')
            )
            ->create();
    }

    #[Test]
    public function get_user_subscribers_successfully(): void
    {
        $response = $this->getJson(route('api.v1.users.subscribers', [
            'user' => $this->getCurrentUserId()
        ]));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'login',
                    'avatar',
                    'isVerified',
                    'isSubscribed'
                ]
            ],
        ]);
    }
}
