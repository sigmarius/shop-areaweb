<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final class ErrorHandler
{
    protected function determineStatusCode(Throwable $exception): int
    {
        return match (true) {
            $exception instanceof AuthenticationException => SymfonyResponse::HTTP_UNAUTHORIZED,
            $exception instanceof NotFoundHttpException => SymfonyResponse::HTTP_NOT_FOUND,
            $exception instanceof ThrottleRequestsException => SymfonyResponse::HTTP_TOO_MANY_REQUESTS,
            $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
            default => SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR
        };
    }

    protected function renderApiError(Throwable $exception, int $statusCode): JsonResponse
    {
        $message = match (true) {
            $exception instanceof ThrottleRequestsException => __('errors.default_core.throttle_error'),
            $statusCode === SymfonyResponse::HTTP_NOT_FOUND
            && $exception->getPrevious() instanceof ModelNotFoundException => getModelNotFoundMessage($exception->getPrevious()->getModel()),
            $statusCode === SymfonyResponse::HTTP_NOT_FOUND
            => __('errors.default_core.route_not_found'),
            $exception instanceof AuthenticationException => __('errors.default_core.auth_required'),
            $statusCode >= SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR => __('errors.default_core.server_error'),
            default => __('errors.default_core.default_error'),
        };

        if ($exception instanceof AuthenticationException) {
            logger()->warning('Auth required for route: ' . request()->path());
        }

        return responseFailed(
            $message,
            $exception->getMessage(),
            $statusCode
        );
    }

    public function handleException(Throwable $exception, Request $request): Response|JsonResponse|null
    {
        $statusCode = $this->determineStatusCode($exception);

        if (
            $request->is('api/*')
            || $request->wantsJson()
        ) {
            return $this->renderApiError($exception, $statusCode);
        }

        // Для веб-запросов - передаём обработку Laravel
        return null; // Вернуть null для стандартной обработки
    }
}
