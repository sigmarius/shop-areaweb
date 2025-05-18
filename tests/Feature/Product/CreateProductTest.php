<?php

namespace Tests\Feature\Product;

use App\Enums\ProductStatusEnum;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // используется дефолтный метод авторизации из TestCase
        $this->signIn();
    }

    #[Test]
    public function create_product_successfully(): void
    {
        $data = [
            'name' => fake()->sentence(),
            'description' => fake()->text(100),
            'count' => fake()->randomNumber(2),
            'price' => fake()->randomNumber(3),
            'state' => fake()->randomElement(ProductStatusEnum::cases())->value,
            'images' => [
                UploadedFile::fake()
                    ->image('image1.jpg', 100, 100)
                    ->size(100),
                UploadedFile::fake()
                    ->image('image2.jpg', 100, 100)
                    ->size(100),
            ],
        ];

        $response = $this
            // если используется метод postJson => withHeaders можно не передавать
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->postJson(route('api.v1.products.store'), $data);

        $response->assertCreated();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'count',
                'rating',
                'images' => [],
                'reviews' => []
            ]
        ]);

        $response->assertJson([
            'data' => [
                'name' => Arr::get($data, 'name'),
                'description' => Arr::get($data, 'description'),
                'price' => Arr::get($data, 'price'),
                'count' => Arr::get($data, 'count'),
                'rating' => 0,
                'reviews' => []
            ]
        ]);

        $this->assertCount(2, $response->json('data.images'));

        $this->assertDatabaseHas(Product::class, [
            'id' => $response->json('data.id'),
            // ID пользователя берем из дефолтного метода авторизации в TestCase
            'user_id' => $this->getCurrentUserId(),
            'name' => $response->json('data.name'),
            'description' => $response->json('data.description'),
            'price' => $response->json('data.price'),
            'count' => $response->json('data.count'),
        ]);
    }

    #[Test]
    public function create_product_validation_failed(): void
    {
        $data = [
            'description' => fake()->text(100),
            'count' => fake()->randomNumber(2),
            'price' => fake()->randomNumber(3),
        ];

        $response = $this
            // если используется метод postJson => withHeaders можно не передавать
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->postJson(route('api.v1.products.store'), $data);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['name', 'state']);
    }
}
