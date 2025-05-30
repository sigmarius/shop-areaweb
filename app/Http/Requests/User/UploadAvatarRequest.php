<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\ApiRequest;
use Illuminate\Http\UploadedFile;

class UploadAvatarRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function getAvatar(): ?UploadedFile
    {
        return $this->file('avatar');
    }
}
