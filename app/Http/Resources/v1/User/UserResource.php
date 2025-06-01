<?php

declare(strict_types=1);

namespace App\Http\Resources\v1\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'login' => $this->login,
            'subscribers' => $this->subscriptionsCount(),
            'publications' => $this->postsCount(),
            'avatar' => $this->avatar,
            'about' => $this->about,
            'isVerified' => $this->is_verified,
            'registeredAt' => $this->created_at->format('d/m/Y H:i'),
            'isSubscribed' => $this->isSubscribed(),
        ];
    }
}
