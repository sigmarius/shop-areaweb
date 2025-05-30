<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\User\CurrentUserResource;

class UserController extends Controller
{
    public function profile(): CurrentUserResource
    {
        return CurrentUserResource::make(auth()->user());
    }
}
