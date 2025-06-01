<?php

declare(strict_types=1);

namespace App\DTOs\User;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateUserDTO extends Data
{
    public function __construct(
        public string|Optional $name,
        public string|Optional $login,
        public string|Optional $email,
        public string|Optional $about,
        #[Computed]
        public string|Optional $password,
    )
    {
        if (is_string($this->password)) {
            $this->password = bcrypt($this->password);
        }
    }
}
