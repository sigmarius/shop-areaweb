<?php

use App\Http\Controllers\Api\v1\ProductController;
use Illuminate\Support\Facades\Route;

Route::apiResource('products', ProductController::class);

Route::controller(ProductController::class)
    ->prefix('products')
    ->group(function (): void {
        Route::post('{product}/review', 'addReview')->name('products.review.store');
    });
