<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\User\InvalidUserCredentialsException;
use App\Facades\UserFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;

class LoginController extends Controller
{
    /**
     * @throws InvalidUserCredentialsException
     */
    public function __invoke(LoginRequest $request)
    {
        return UserFacade::login($request->toDTO());
    }
}
