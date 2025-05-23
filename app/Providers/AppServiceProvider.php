<?php

namespace App\Providers;

use App\Services\Product\ProductService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // биндим фасад - в первом аргументе указываем значение,
        // возвращаемое getFacadeAccessor() из созданного фасада ProductFacade
        // вторым аргументом указываем сервис, который хотим прибиндить к этому фасаду
        $this->app->bind('product', ProductService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
