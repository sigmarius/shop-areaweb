<?php

use Illuminate\Http\JsonResponse;
use \Symfony\Component\HttpFoundation\Response;

function responseOk(): JsonResponse
{
    return response()->json([
        'status' => 'success',
    ]);
}

function responseFailed(
    ?string $message = null,
    ?string $error = null,
    int $code = Response::HTTP_BAD_REQUEST
): JsonResponse
{
    return response()->json([
        'status' => 'error',
        'message' => $message ?? __('errors.default_core.default_error'),
        'error' => $error,
    ], $code);
}
