<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use App\DTOs\Post\AddCommentDTO;
use App\Http\Requests\ApiRequest;

class AddPostCommentRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'max:255'],
        ];
    }

    public function toDTO(): AddCommentDTO
    {
        return AddCommentDTO::from([
            'user_id' => auth()->id(),
            'comment' => $this->input('comment'),
        ]);
    }
}
