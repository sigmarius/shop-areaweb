<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // используется дефолтный метод авторизации из TestCase
        $this->signIn();
    }

    #[Test]
    public function success_auth_with_email(): void
    {
        $response = $this->postJson(route('api.v1.user.login'), [
            'email' => $this->currentUser->email,
            'password' => 'password'
        ]);

        $response->assertOk();

        $response->assertJsonStructure(['token']);
    }

    #[Test]
    public function success_auth_with_login(): void
    {
        $response = $this->postJson(route('api.v1.user.login'), [
            'login' => $this->currentUser->login,
            'password' => 'password'
        ]);

        $response->assertOk();

        $response->assertJsonStructure(['token']);
    }
}
