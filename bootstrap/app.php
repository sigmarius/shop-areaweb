<?php

declare(strict_types=1);

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\DraftProductMiddleware;
use App\Http\Middleware\PostAccessMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
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
        $exceptions->render(function (ThrottleRequestsException $exception) {
            return responseFailed(
                __('errors.default_core.throttle_error'),
                $exception->getMessage(),
                SymfonyResponse::HTTP_TOO_MANY_REQUESTS
            );
        });
        $exceptions->render(function (NotFoundHttpException $exception) {
            $message = $exception->getPrevious() instanceof ModelNotFoundException
                ? getModelNotFoundMessage($exception->getPrevious()->getModel())
                : __('errors.default_core.route_not_found');

            return responseFailed(
                $message,
                $exception->getMessage(),
                SymfonyResponse::HTTP_NOT_FOUND
            );
        });
        $exceptions->render(function (AuthenticationException $exception) {
            logger()->warning('Auth required for route: ' . request()->path());

            return responseFailed(
                __('errors.default_core.auth_required'),
                $exception->getMessage(),
                SymfonyResponse::HTTP_UNAUTHORIZED
            );
        });
        $exceptions->render(function (Throwable $exception) {
            // если это внутренняя ошибка сервера или любая ошибка 5xx
            $statusCode = SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR;

            // можно использовать HttpExceptionInterface, если статус известен
            if ($exception instanceof Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $statusCode = $exception->getStatusCode();
            }

            // разделяем ошибки на серверные и клиентские, чтобы отдавать их на фронт
            $message = $statusCode >= 500
                ? __('errors.default_core.server_error')
                : __('errors.default_core.default_error');

            return responseFailed(
                $message,
                $exception->getMessage(),
                $statusCode
            );
        });
    })->create();
