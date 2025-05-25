<?php

declare(strict_types=1);

namespace App\Services\User;

use App\DTOs\User\RegisterUserDTO;
use App\Http\Resources\v1\User\CurrentUserResource;
use App\Models\User;

class UserService
{
    public function store(RegisterUserDTO $data): CurrentUserResource
    {
        return CurrentUserResource::make(
            User::query()
                ->create($data->toArray())
        );
    }
}
