<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('products', ProductController::class);

Route::controller(ProductController::class)
    ->prefix('products')
    ->group(function () {
        Route::post('{product}/review', 'addReview')->name('products.review.store');
    });
