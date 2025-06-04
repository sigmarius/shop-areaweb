<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use App\Http\Requests\ApiRequest;

class GetPostsFeedRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'limit' => ['required', 'integer', 'min:1', 'max:100'],
            'offset' => ['required', 'integer', 'min:0'],
        ];
    }

    public function limit(): int
    {
        return (int)$this->input('limit');
    }

    public function offset(): int
    {
        return (int)$this->input('offset');
    }
}
