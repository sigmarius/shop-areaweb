<?php

declare(strict_types=1);

namespace App\DTOs\Post;

use Spatie\LaravelData\Data;

class AddCommentDTO extends Data
{
    public function __construct(
        public int    $user_id,
        public string $comment,
    )
    {
    }
}
