<?php

declare(strict_types=1);

namespace App\DTOs\Post;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdatePostDTO extends Data
{
    public function __construct(
        public string|Optional|null $description,
    )
    {
    }
}
