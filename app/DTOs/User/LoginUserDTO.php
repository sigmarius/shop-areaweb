<?php

declare(strict_types=1);

namespace App\DTOs\User;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class LoginUserDTO extends Data
{
    public function __construct(
        public string|Optional $email,
        public string|Optional $login,
        public string          $password,
    )
    {
    }
}
