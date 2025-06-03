<?php

declare(strict_types=1);

use App\Http\Controllers\Api\v1\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class);
});
