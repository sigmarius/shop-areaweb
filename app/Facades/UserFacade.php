<?php

declare(strict_types=1);

namespace App\Facades;

use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static User store(array $data)
 *
 * @see UserService
 */
class UserFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return UserService::class;
    }
}
