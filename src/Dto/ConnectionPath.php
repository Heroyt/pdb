<?php
declare(strict_types=1);

namespace App\Dto;

use App\Models\Factory;
use OpenApi\Attributes as OA;

#[OA\Schema]
class ConnectionPath
{

    /** @var FactoryConnectionWithCost[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/FactoryConnectionWithCost'))]
    public array $path = [];

    public function __construct(
      #[OA\Property]
      public Factory $start,
      #[OA\Property]
      public Factory $end,
      #[OA\Property]
      public float $totalCost,
    ) {}

}