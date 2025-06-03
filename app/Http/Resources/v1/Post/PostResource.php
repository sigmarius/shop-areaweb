<?php

declare(strict_types=1);

namespace App\Http\Resources\v1\Post;

use App\Http\Resources\v1\Comment\CommentResource;
use App\Http\Resources\v1\User\MinifiedUserResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Post */
class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'photo' => $this->photo,

            'user' => new MinifiedUserResource($this->user),

            'description' => $this->description,

            'likes' => $this->totalLikes(),
            'isLiked' => $this->isLiked(),

            'comments' => [
                'total' => $this->totalComments(),
                'list' => CommentResource::collection($this->comments)
            ],

            'createdAt' => $this->created_at->format('d/m/Y H:i'),
        ];
    }
}
