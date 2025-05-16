<?php

namespace App\DTOs;

use App\Enums\ProductStatusEnum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CreateProductDTO extends Data
{
   public string $name;
   public string|Optional $description;
   public int|float $price;

   public int $count;

   // если поле в реквесте ('state') отличается от названия поля в БД ('status')
   // используем аттрибут MapInputName
   #[MapInputName('state')]
   public ProductStatusEnum $status;
   public array|Optional $images;
}
