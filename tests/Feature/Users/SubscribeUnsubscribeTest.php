<?php

namespace Tests\Feature\Users;

use App\Enums\SubscribeState;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubscribeUnsubscribeTest extends TestCase
{
    use RefreshDatabase;

    private User $unsubscribedUser;

    private User $subscribedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();

        $this->unsubscribedUser = User::factory()
            ->create();

        $this->subscribedUser = User::factory()
            ->create();

        Subscription::factory()->create([
            'user_id' => $this->subscribedUser->id,
            'subscriber_id' => $this->getCurrentUserId(),
        ]);
    }

    #[Test]
    public function subscribe_to_unsubscribed_user(): void
    {
        $response = $this->postJson(route('api.v1.users.subscribe', [
            'user' => $this->getCurrentUserId()
        ]));

        $response->assertOk();

        $this->assertEquals(SubscribeState::Subscribed->value, $response->json('state'));
    }

    #[Test]
    public function unsubscribe_from_subscribed_user(): void
    {
        $response = $this->postJson(route('api.v1.users.subscribe', [
            'user' => $this->subscribedUser->id
        ]));

        $response->assertOk();

        $this->assertEquals(SubscribeState::Unsubscribed->value, $response->json('state'));
    }
}
