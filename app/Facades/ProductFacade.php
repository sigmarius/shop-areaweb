<?php

namespace App\Facades;

use App\Services\Product\ProductService;
use Illuminate\Support\Facades\Facade;

/**
 * Чтобы IDE понимала, к какому сервису относится фасад:
 * @see ProductService
 */
class ProductFacade extends Facade
{
    /**
     * Этот метод должен обязательно присутствовать!
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        // возвращаем строку в качестве идентификатора
        return 'product';
    }
}
