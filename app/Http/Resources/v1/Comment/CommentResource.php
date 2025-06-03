<?php

declare(strict_types=1);

namespace App\Http\Resources\v1\Comment;

use App\Http\Resources\v1\User\MinifiedUserResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Comment */
class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'created_at' => $this->created_at->format('d/m/Y H:i'),

            'user' => MinifiedUserResource::make($this->user),
        ];
    }
}
