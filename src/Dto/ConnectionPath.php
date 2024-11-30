<?php
declare(strict_types=1);

namespace App\Dto;

use App\Models\Factory;

class ConnectionPath
{

    /** @var FactoryConnectionWithCost[] */
    public array $path = [];

    public function __construct(
      public Factory $start,
      public Factory $end,
      public float $totalCost,
    ) {}

}