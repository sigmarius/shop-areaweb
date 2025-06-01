<?php

declare(strict_types=1);

use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)
        ->prefix('/users')
        ->as('users.')
        ->group(function () {
            Route::get('/{user}', 'getUser')
                ->name('get-user');

            // Информация о подписчиках пользователя, ID которого передан в Path Variables
            Route::get('/{user}/subscribers', 'userSubscribers')
                ->name('subscribers');

            Route::post('/{user}/subscribe', 'subscribe')
                ->name('subscribe');
        });
});
