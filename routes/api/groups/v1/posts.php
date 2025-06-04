<?php

declare(strict_types=1);

use App\Http\Controllers\Api\v1\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class);

    Route::controller(PostController::class)
        ->prefix('posts')
        ->as('posts.')
        ->group(function () {
            Route::post('{post}/like', 'like')
                ->name('like');

            Route::post('{post}/comment', 'addComment')
                ->name('comment');
        });
});
