<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\DTOs\User\LoginUserDTO;
use App\Http\Requests\ApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class LoginRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required'],
        ];
    }

    public function toDTO(): LoginUserDTO
    {
        return LoginUserDTO::from($this->validated());
    }
}
