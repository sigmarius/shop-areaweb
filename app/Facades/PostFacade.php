<?php

declare(strict_types=1);

namespace App\Facades;

use App\DTOs\Post\StorePostDTO;
use App\DTOs\Post\UpdatePostDTO;
use App\Models\Post;
use App\Services\Post\PostService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Post store(StorePostDTO $data)
 * @method static Post update(UpdatePostDTO $data)
 * @method static Collection feed(int $limit, int $offset)
 * @method static int|null totalFeedPosts()
 *
 * @see PostService
 */
class PostFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PostService::class;
    }
}
