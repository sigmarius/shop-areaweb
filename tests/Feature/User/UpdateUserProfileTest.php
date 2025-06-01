<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateUserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    #[Test]
    public function profile_updated_successfully(): void
    {
        $data = [
            'name' => fake()->name,
            'login' => fake()->unique()->userName,
            'email' => fake()->unique()->email,
            'about' => fake()->text,
        ];

        $response = $this->patchJson(route('api.v1.user.update'), $data);

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

        $response->assertJson([
            'data' => [
                'name' => Arr::get($data, 'name'),
                'email' => Arr::get($data, 'email'),
                'about' => Arr::get($data, 'about'),
            ]
        ]);

        $this->assertDatabaseHas(User::class, [
            'id' => $this->getCurrentUserId(),
            'name' => Arr::get($data, 'name'),
            'login' => Arr::get($data, 'login'),
            'email' => Arr::get($data, 'email'),
            'about' => Arr::get($data, 'about'),
        ]);
    }

    #[Test]
    public function validation_works_correctly(): void
    {
        // Данные, которые должны вызвать ошибки валидации
        $invalidData = [
            'email' => 'not-an-email', // Невалидный email
            'login' => $this->getCurrentUser()->login, // Уже занятый login
            'name' => str_repeat('a', 256), // Слишком длинное имя (>255 символов)
            'password' => 'short', // Пароль без подтверждения и слишком короткий
        ];

        $response = $this->patchJson(route('api.v1.user.update'), $invalidData);

        $response->assertUnprocessable(); // 422 статус
        $response->assertJsonValidationErrors([
            'email',
            'login',
            'name',
            'password',
        ]);
    }

    #[Test]
    public function password_updates_successfully(): void
    {
        $newPassword = 'new_secure_password_123';

        // Пытаемся обновить пароль
        $response = $this->patchJson(route('api.v1.user.update'), [
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertOk();

        // Проверяем, что пароль действительно изменился в БД
        $this->assertTrue(
            Hash::check($newPassword, $this->getCurrentUser()->fresh()->password),
            'Пароль не обновился!'
        );
    }

    #[Test]
    public function password_requires_confirmation(): void
    {
        // Пытаемся обновить пароль без подтверждения
        $response = $this->patchJson(route('api.v1.user.update'), [
            'password' => 'new_password',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('password');
    }
}
