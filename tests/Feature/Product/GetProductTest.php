<?php

namespace Tests\Feature\Product;

use App\Enums\ProductStatusEnum;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetProductTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;
    private Product $draftProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()
            ->for(User::factory())
            ->has(
                ProductReview::factory(5)
                    ->for(User::factory()),
                'reviews'
            )
            ->has(ProductImage::factory(3), 'images')
            ->createOne(['status' => ProductStatusEnum::Published]);

        $this->draftProduct = Product::factory()
            ->createOne(['status' => ProductStatusEnum::Draft]);
    }

    #[Test]
    public function get_product(): void
    {
        $response = $this->get(route('api.v1.products.show', [
            'product' => $this->product
        ]));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'count',
                'rating',
                'images',
                'reviews' => [
                    '*' => [
                        'id', 'userName', 'text', 'rating'
                    ]
                ]
            ]
        ]);

        $response->assertJson([
            'data' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'description' => $this->product->description,
                'price' => $this->product->price,
                'count' => $this->product->count,
                'rating' => $this->getRating(),
                'images' => $this->getImagesArray(),
                'reviews' => $this->getReviews()
            ]
        ]);
    }

    #[Test]
    public function get_draft_product(): void
    {
        $response = $this->get(route('api.v1.products.show', [
            'product' => $this->draftProduct->id
        ]));

        $response->assertNotFound();

        $response->assertJsonStructure([
            'status', 'message', 'error'
        ]);

        $response->assertJson([
            'status' => 'error',
            'message' => __('errors.product.not_found'),
            'error' => 'Product is draft'
        ]);
    }

    #[Test]
    public function get_product_not_found(): void
    {
        $response = $this->get(route('api.v1.products.show', [
            'product' => 0
        ]));

        $response->assertNotFound();

        $response->assertJsonStructure([
            'status', 'message', 'error'
        ]);

        $response->assertJson([
            'status' => 'error',
            'message' => __('errors.default_core.model_not_found', ['model' => 'Product']),
            'error' => 'No query results for model [App\\Models\\Product] 0'
        ]);
    }

    private function getRating(): int|float
    {
        return round($this->product->reviews->avg('rating'), 1);
    }

    private function getImagesArray(): array
    {
        return $this->product->images
            ->map(fn ($image) => $image->url)
            ->toArray();
    }

    private function getReviews(): array
    {
        return $this->product->reviews
            ->map(fn(ProductReview $review) => [
                'id' => $review->id,
                'userName' => $review->user->name,
                'text' => $review->text,
                'rating' => $review->rating,
            ])
            ->toArray();
    }
}
