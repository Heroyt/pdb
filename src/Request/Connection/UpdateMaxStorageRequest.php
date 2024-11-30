<?php

declare(strict_types=1);

namespace App\Request\Connection;

use App\Models\ConnectionStorage;
use App\Models\Material;
use App\Request\Attributes\IntRange;
use App\Request\UpdateRequest;
use OpenApi\Attributes as OA;

/**
 * @extends UpdateRequest<ConnectionStorage>
 */
#[OA\Schema(schema: 'ConnectionUpdateMaxStorageRequest')]
final class UpdateMaxStorageRequest extends UpdateRequest
{
    #[OA\Property]
    public Material $material;
    /** @var int<0,max> */
    #[OA\Property(description: 'Maximum amount of material that should be automatically loaded'), IntRange(min: 0)]
    public int $maxQuantity;
}
