<?php

declare(strict_types=1);

namespace App\DTOs\User;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class RegisterUserDTO extends Data
{
    public function __construct(
        public string $name,
        public string $login,
        public string $email,
        #[Computed]
        public string $password,
    )
    {
        $this->password = bcrypt($this->password);
    }
}
