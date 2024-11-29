<?php

declare(strict_types=1);

namespace App\Request\Connection;

use App\Models\Factory;
use App\Request\Attributes\IntRange;
use App\Request\Attributes\ModelExists;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "ConnectionCreateRequest")]
final class CreateRequest
{

    public Factory $start {
        get => Factory::get($this->startId);
    }

    public Factory $end {
        get => Factory::get($this->endId);
    }

    /**
     * @param  int<1,max>  $startId
     * @param  int<1,max>  $endId
     * @param  int<1,max>  $speed
     * @param  int<1,max>  $capacity
     */
    public function __construct(
      #[ModelExists(Factory::class), OA\Property(description: 'Start factory ID')]
      public readonly int $startId,
      #[ModelExists(Factory::class), OA\Property(description: 'End factory ID')]
      public readonly int $endId,
      #[IntRange(min: 1), OA\Property(minimum: 1)]
      public readonly int $speed,
      #[IntRange(min: 1), OA\Property(minimum: 1)]
      public readonly int $capacity,
    ) {}

}
