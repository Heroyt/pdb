<?php

declare(strict_types=1);

namespace App\Request\Material;

use App\Request\Attributes\IntRange;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "MaterialCreateRequest")]
final readonly class CreateRequest
{
    /**
     * @param  non-empty-string  $name
     * @param  int<1,max>  $size
     */
    public function __construct(
      #[OA\Property]
      public string $name,
      #[OA\Property(minimum: 1), IntRange(min: 1)]
      public int    $size,
      #[OA\Property(description: 'Used in processes where concrete materials do not matter')]
      public bool   $wildcard = false,
    ) {}
}
