<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\DTOs\User\RegisterUserDTO;
use App\Http\Requests\ApiRequest;

class RegisterRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'login' => ['required', 'unique:users,login'],
            'password' => ['required', 'confirmed'],
        ];
    }

    public function toDTO(): RegisterUserDTO
    {
        return RegisterUserDTO::from($this->validated());
    }
}
