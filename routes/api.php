<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('products', ProductController::class);

Route::controller(ProductController::class)
    ->prefix('products')
    ->group(function () {
        Route::post('{product}/review', 'addReview')->name('products.review.store');
    });

Route::controller(UserController::class)->group(function () {
    Route::post('/login', 'login')->name('login');
});
