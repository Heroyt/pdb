<?php
declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema]
class FactoryConnectionWithCost extends FactoryConnection
{

    #[OA\Property]
    public float $cost = 0.0;

}