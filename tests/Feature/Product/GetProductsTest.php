<?php

namespace Tests\Feature\Product;

use App\Enums\ProductStatusEnum;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetProductsTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        // генерируем тестовые продукты
        Product::factory(5)
            ->for(User::factory())
            ->create(['status' => ProductStatusEnum::Published]);
    }

    #[Test]
    public function get_products_successfully(): void
    {
        $response = $this->get(route('api.v1.products.index'));

        $response->assertOk();

        $response->assertJsonStructure([
           'data' => [
               '*' => [
                   'id', 'name', 'price', 'rating'
               ]
           ]
        ]);
    }
}
