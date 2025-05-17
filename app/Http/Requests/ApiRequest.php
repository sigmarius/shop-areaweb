<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Наследование от этого класса позволяет не передавать каждый раз в запросе заголовок Accept: application/json
 */
class ApiRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $errorCount = $validator->errors()->count();

        $firstError = $validator->errors()->first();

        $messageAdditionalText = $errorCount > 1
            ? ' (' . trans_choice('validation.errors_count', $errorCount - 1, ['count' => $errorCount - 1]) . ')'
            : '';

        throw new HttpResponseException(response()->json([
            'message' => $firstError . $messageAdditionalText,
            'errors' => $validator->getMessageBag(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
