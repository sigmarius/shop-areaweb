<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Facades\UserFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserProfileRequest;
use App\Http\Requests\User\UploadAvatarRequest;
use App\Http\Resources\v1\User\CurrentUserResource;

class UserController extends Controller
{
    public function profile(): CurrentUserResource
    {
        return CurrentUserResource::make(auth()->user());
    }

    public function uploadAvatar(UploadAvatarRequest $request): CurrentUserResource
    {
        return CurrentUserResource::make(
            UserFacade::uploadAvatar($request->getAvatar())
        );
    }

    public function updateProfile(UpdateUserProfileRequest $request): CurrentUserResource
    {
        return CurrentUserResource::make(
            UserFacade::update($request->toDTO())
        );
    }
}
