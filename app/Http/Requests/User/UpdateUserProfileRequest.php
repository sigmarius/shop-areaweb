<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\DTOs\User\UpdateUserDTO;
use App\Http\Requests\ApiRequest;

class UpdateUserProfileRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'max:255'],
            'login' => ['nullable', 'unique:users,login', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email', 'max:255'],
            'about' => ['nullable', 'max:255'],
            'password' => ['nullable', 'confirmed'],
        ];
    }

    public function toDTO(): UpdateUserDTO
    {
        return UpdateUserDTO::from($this->validated());
    }
}
