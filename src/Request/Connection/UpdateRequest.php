<?php

declare(strict_types=1);

namespace App\Request\Connection;

use App\Models\Connection;
use App\Request\Attributes\IntRange;
use OpenApi\Attributes as OA;

/**
 * @extends \App\Request\UpdateRequest<Connection>
 */
#[OA\Schema(schema: "ConnectionUpdateRequest")]
class UpdateRequest extends \App\Request\UpdateRequest
{
    #[OA\Property(nullable: true)]
    public bool $assigned;
    #[OA\Property(nullable: true)]
    public bool $active;
    #[OA\Property(nullable: true), IntRange(min: 1)]
    public int $speed;
    #[OA\Property(nullable: true), IntRange(min: 1)]
    public int $storageCapacity;
}
