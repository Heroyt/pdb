<?php
declare(strict_types=1);

namespace App\Enums;

use OpenApi\Attributes as OA;

#[OA\Schema(type: 'string')]
enum Direction : string
{

    case IN  = 'in';
    case OUT = 'out';

}
