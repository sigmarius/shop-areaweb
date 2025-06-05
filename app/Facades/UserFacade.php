<?php

declare(strict_types=1);

namespace App\Facades;

use App\DTOs\User\LoginUserDTO;
use App\DTOs\User\RegisterUserDTO;
use App\DTOs\User\UpdateUserDTO;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Facade;

/**
 * @method static User store(RegisterUserDTO $data)
 * @method static array login(LoginUserDTO $data)
 * @method static User uploadAvatar(UploadedFile $avatar)
 * @method static User update(UpdateUserDTO $data)
 * @method static Collection ownPosts(User $user, int $limit, int $offset)
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
