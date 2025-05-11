<?php

namespace App\Http\Resources\Product;

use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductReview
 */
class ProductReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userName' => $this->user->name,
            'text' => $this->text,
            'rating' => $this->rating
        ];
    }
}
