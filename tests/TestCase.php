<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Product\CreateProductTest;

abstract class TestCase extends BaseTestCase
{
    private User $currentUser;

    /**
     * Дефолтная авторизация пользователя в системе -
     * используется в методах, где нужна авторизация
     * @see CreateProductTest::signIn()
     *
     * @param bool $isAdmin
     * @return void
     */
    protected function signIn(bool $isAdmin = true): void
    {
        $this->currentUser = User::factory()->create([
            'is_admin' => $isAdmin
        ]);

        // имитируем авторизацию в Sanctum
        Sanctum::actingAs($this->currentUser);
    }

    protected function getCurrentUserId(): int
    {
        return $this->currentUser->id;
    }

    protected function getCurrentUser(): User
    {
        return $this->currentUser;
    }
}
