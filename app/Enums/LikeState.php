<?php

declare(strict_types=1);

namespace App\Enums;

enum LikeState: string
{
    case Liked = 'liked';
    case Unliked = 'unliked';
}
