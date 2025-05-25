<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function register_validation(): void
    {
        $response = $this->postJson(route('api.v1.user.register'), [
            'name' => null,
            'login' => null,
            'email' => 'sigmariusmail.ru',
            'password' => 'password',
            'password_confirmation' => ''
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['name', 'login', 'email', 'password']);

        $response->assertJsonValidationErrorFor('login');
    }

    #[Test]
    public function user_register_successfully(): void
    {
        $data = [
            'name' => fake()->name(),
            'login' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson(route('api.v1.user.register'), $data);

        $response->assertCreated();

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

        $response->assertJson([
            'data' => [
                'name' => Arr::get($data, 'name'),
                'email' => Arr::get($data, 'email'),
                'subscribers' => 0,
                'publications' => 0,
                'avatar' => null,
                'about' => null,
                'isVerified' => false,
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $response->json('data.id'),
            'name' => $response->json('data.name'),
            'login' => Arr::get($data, 'login'),
            'email' => $response->json('data.email'),
        ]);
    }
}
