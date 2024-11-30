<?php

declare(strict_types=1);

namespace App\Dto;

use App\Models\Connection;
use App\Models\Factory;
use OpenApi\Attributes as OA;

#[OA\Schema]
class FactoryConnection
{
    public function __construct(
      #[OA\Property]
      public Factory    $start,
      #[OA\Property]
      public Connection $connection,
      #[OA\Property]
      public Factory    $end,
    ) {}
}
