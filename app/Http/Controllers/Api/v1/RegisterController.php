<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Facades\UserFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        return UserFacade::store($request->toDTO());
    }
}
