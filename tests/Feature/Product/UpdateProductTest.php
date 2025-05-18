<?php

namespace Product;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // используется дефолтный метод авторизации из TestCase
        $this->signIn();

        $this->product = Product::factory()
            ->createOne();
    }

    #[Test]
    public function update_product_successfully(): void
    {
        $data = [
            'name' => fake()->sentence(),
            'price' => fake()->randomNumber(3),
        ];

        $response = $this
            // если используется метод postJson => withHeaders можно не передавать
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->putJson(route('api.v1.products.update', ['product' => $this->product->id]), $data);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'count',
                'rating',
                'images' => [],
                'reviews' => [
                    '*' => [
                        'id', 'userName', 'text', 'rating'
                    ]
                ]
            ]
        ]);

        $response->assertJson([
            'data' => [
                'name' => Arr::get($data, 'name'),
                'price' => Arr::get($data, 'price'),
            ]
        ]);

        $this->assertDatabaseHas(Product::class, [
            'id' => $this->product->id,
            'name' => Arr::get($data, 'name'),
            'price' => Arr::get($data, 'price'),
            'count' => $response->json('data.count'),
        ]);
    }
}
