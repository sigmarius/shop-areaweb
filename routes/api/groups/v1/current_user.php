<?php

declare(strict_types=1);

use App\Http\Controllers\Api\v1\LoginController;
use App\Http\Controllers\Api\v1\RegisterController;
use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/user', 'as' => 'user.'], function (): void {
    Route::post('/register', RegisterController::class)
        ->name('register');

    Route::post('/login', LoginController::class)
        ->name('login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::controller(UserController::class)->group(function (): void {
            Route::get('/', 'profile')
                ->name('profile');

            Route::patch('/', 'updateProfile')
                ->name('update');

            Route::post('avatar', 'uploadAvatar')
                ->name('avatar.upload');
        });
    });
});
