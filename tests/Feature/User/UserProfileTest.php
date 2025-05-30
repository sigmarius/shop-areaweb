<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // используется дефолтный метод авторизации из TestCase
        $this->signIn(false);
    }

    #[Test]
    public function user_returns_successfully(): void
    {
        $response = $this->get(route('api.v1.user.profile'));

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
                'registeredAt'
            ],
        ]);

        //TODO проверить возвращаемые данные
    }
}
