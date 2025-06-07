<?php

declare(strict_types=1);

namespace App\Services\User;

use App\DTOs\User\LoginUserDTO;
use App\DTOs\User\RegisterUserDTO;
use App\DTOs\User\UpdateUserDTO;
use App\Exceptions\User\InvalidUserCredentialsException;
use App\Http\Resources\v1\User\CurrentUserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\NewAccessToken;

class UserService
{
    public function store(RegisterUserDTO $data): CurrentUserResource
    {
        return CurrentUserResource::make(
            User::query()
                ->create($data->toArray())
        );
    }

    /**
     * @throws InvalidUserCredentialsException
     */
    public function login(LoginUserDTO $data): array
    {
        // guard 'api' не имеет метода attempt, поэтому используем guard 'web'
        if (!auth()->guard('web')->attempt($data->toArray())) {
            throw new InvalidUserCredentialsException();
        }

        // если мы проверяли пользователя через guard 'web' - получаем его также через guard 'web'
        /** @var NewAccessToken $token */
        $token = auth()->guard('web')->user()
            ->createToken('api_login');

        return [
            'token' => $token->plainTextToken,
        ];
    }

    public function uploadAvatar(UploadedFile $avatar): User
    {
        auth()->user()->update([
            'avatar' => uploadImage($avatar, 'avatars')
        ]);

        return auth()->user();
    }

    public function update(UpdateUserDTO $data): User
    {
        auth()->user()->update($data->toArray());

        return auth()->user();
    }

    public function ownPosts(User $user, int $limit = 10, int $offset = 0): Collection
    {
        return $user->posts()
            ->with(['user', 'likes', 'comments'])
            ->limit($limit)
            ->offset($offset)
            ->orderByDesc('created_at')
            ->get();
    }
}
