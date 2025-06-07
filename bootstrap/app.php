<?php

declare(strict_types=1);

use App\Exceptions\ErrorHandler;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\DraftProductMiddleware;
use App\Http\Middleware\PostAccessMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('api')
                ->prefix('api/v1')
                ->name('api.v1.')
                ->group(__DIR__.'/../routes/api/api_v1.php');

            Route::middleware('api')
                ->prefix('api/v2')
                ->name('api.v2.')
                ->group(__DIR__.'/../routes/api/api_v2.php');
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'product.draft' => DraftProductMiddleware::class,
            'post.access' => PostAccessMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $exception, Request $request) {
            return app(ErrorHandler::class)
                ->handleException($exception, $request);
        });
    })->create();
