<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v2\ProductController;

Route::apiResource('products', ProductController::class)
    ->only(['index']);
