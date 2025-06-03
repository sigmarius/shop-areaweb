<?php

declare(strict_types=1);

namespace App\DTOs\Post;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class StorePostDTO extends Data
{
    public function __construct(
        public UploadedFile         $photo,
        public string|Optional|null $description,
    )
    {
    }
}
