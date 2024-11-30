<?php
declare(strict_types=1);

namespace App\Dto;

use App\Models\Factory;
use OpenApi\Attributes as OA;

#[OA\Schema]
class SimpleFactory
{

    public function __construct(
      #[OA\Property]
      public int $id,
      #[OA\Property]
      public string $name,
      #[OA\Property]
      public int $storageCapacity,
    ){}

    public static function fromFactory(Factory $factory): self {
        return new self(
          $factory->id,
          $factory->name,
          $factory->storageCapacity
        );
    }
}