<?php

use App\Http\Controllers\Api\v1\RegisterController;
use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function () {
    Route::post('/login', 'login')->name('login');
});

Route::group(['prefix' => '/user', 'as' => 'user.'], function () {
    Route::post('/register', RegisterController::class)
        ->name('register');
});
