<?php

declare(strict_types=1);

namespace App\Services\User;

use App\DTOs\User\LoginUserDTO;
use App\DTOs\User\RegisterUserDTO;
use App\Exceptions\User\InvalidUserCredentialsException;
use App\Http\Resources\v1\User\CurrentUserResource;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        $path = $avatar->storePublicly('avatars');

        $url = Storage::url($path);

        auth()->user()->update([
            'avatar' => $url
        ]);

        return auth()->user();
    }
}
