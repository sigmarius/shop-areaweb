<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

if (!function_exists('responseOk')) {
    function responseOk(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
        ]);
    }
}

if (!function_exists('responseFailed')) {
    function responseFailed(
        ?string $message = null,
        ?string $error = null,
        int     $code = Response::HTTP_BAD_REQUEST
    ): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message ?? __('errors.default_core.default_error'),
            'error' => $error,
        ], $code, options: JSON_UNESCAPED_UNICODE);
    }
}


if (!function_exists('getModelNotFoundMessage')) {
    function getModelNotFoundMessage(string $model): string
    {
        return match ($model) {
            'App\Models\User' => __('errors.default_core.model_not_found', ['model' => 'User']),
            'App\Models\Product' => __('errors.default_core.model_not_found', ['model' => 'Product']),
            default => Str::replace(
                ' :model',
                '',
                __('errors.default_core.model_not_found')
            ),
        };
    }
}

if (!function_exists('uploadImage')) {
    function uploadImage(UploadedFile $image, string $folderName): string
    {
        $path = $image->storePublicly($folderName);

        return Storage::url($path);
    }
}
