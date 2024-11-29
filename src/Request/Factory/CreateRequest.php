<?php

declare(strict_types=1);

namespace App\Request\Factory;

use App\Request\Attributes\IntRange;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "FactoryCreateRequest")]
final readonly class CreateRequest
{
    /**
     * @param  non-empty-string  $name
     * @param  int<1,max>  $capacity
     */
    public function __construct(
      #[OA\Property]
      public string $name,
      #[OA\Property(minimum: 1), IntRange(min: 1)]
      public int    $capacity,
    ) {}
}
