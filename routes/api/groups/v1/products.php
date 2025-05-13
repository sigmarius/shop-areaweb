<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ProductController;

Route::apiResource('products', ProductController::class);

Route::controller(ProductController::class)
    ->prefix('products')
    ->group(function () {
        Route::post('{product}/review', 'addReview')->name('products.review.store');
    });
