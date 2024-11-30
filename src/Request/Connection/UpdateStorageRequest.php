<?php

declare(strict_types=1);

namespace App\Request\Connection;

use App\Models\ConnectionStorage;
use App\Models\Material;
use App\Request\UpdateRequest;
use OpenApi\Attributes as OA;

/**
 * @extends UpdateRequest<ConnectionStorage>
 */
#[OA\Schema(schema: 'ConnectionUpdateStorageRequest')]
final class UpdateStorageRequest extends UpdateRequest
{
    #[OA\Property]
    public Material $material;
    #[OA\Property(description: 'Quantity difference (positive for adding stock, negative for removing).')]
    public int $quantity;
}
