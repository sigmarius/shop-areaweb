<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Facades\UserFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\GetPostsFeedRequest;
use App\Http\Requests\User\UpdateUserProfileRequest;
use App\Http\Requests\User\UploadAvatarRequest;
use App\Http\Resources\v1\Post\PostFeedResource;
use App\Http\Resources\v1\User\CurrentUserResource;
use App\Http\Resources\v1\User\UserResource;
use App\Http\Resources\v1\User\UserSubscribersResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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

    public function getUser(User $user): UserResource
    {
        return UserResource::make($user);
    }

    /**
     * Информация о подписчиках пользователя, ID которого передан в Path Variables
     *
     * @param User $user
     * @return AnonymousResourceCollection
     */
    public function userSubscribers(User $user): AnonymousResourceCollection
    {
        return UserSubscribersResource::collection($user->subscriptions);
    }

    public function subscribe(User $user)
    {
        return response()->json([
            'state' => $user->subscribe()
        ]);
    }

    public function ownPosts(User $user, GetPostsFeedRequest $request): JsonResponse
    {
        return response()->json([
            'posts' => PostFeedResource::collection(UserFacade::ownPosts($user, $request->limit, $request->offset)),
            'total' => $user->postsCount()
        ]);
    }
}
