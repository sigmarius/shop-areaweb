<?php

declare(strict_types=1);

namespace App\Facades;

use App\DTOs\Post\StorePostDTO;
use App\DTOs\Post\UpdatePostDTO;
use App\Models\Post;
use App\Services\Post\PostService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Post store(StorePostDTO $data)
 * @method static Post update(UpdatePostDTO $data)
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
