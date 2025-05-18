<?php

namespace Product;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteProductTest extends TestCase
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
    public function delete_product_successfully(): void
    {
        $response = $this
            // если используется метод postJson => withHeaders можно не передавать
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->deleteJson(route('api.v1.products.destroy', ['product' => $this->product]));

        $response->assertOk();

        $this->assertDatabaseMissing(Product::class, [
            'id' => $this->product->id
        ]);
    }
}
