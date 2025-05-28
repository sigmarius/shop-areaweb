<?php

declare(strict_types=1);

namespace App\Exceptions\User;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class InvalidUserCredentialsException extends Exception
{
    public function __construct(
        string     $message = null,
        int        $code = 0,
        ?Throwable $previous = null
    )
    {
        parent::__construct(
            $message ?? __('errors.user.invalid_credentials'),
            Response::HTTP_UNAUTHORIZED,
            $previous
        );
    }

    public function render(Request $request): Response
    {
        return responseFailed(
            $this->getMessage(),
            'Invalid user credentials',
            $this->getCode()
        );
    }
}
