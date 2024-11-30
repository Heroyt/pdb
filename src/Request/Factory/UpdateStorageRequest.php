<?php

declare(strict_types=1);

namespace App\Request\Factory;

use App\Models\FactoryStorage;
use App\Models\Material;
use App\Request\UpdateRequest;
use OpenApi\Attributes as OA;

/**
 * @extends UpdateRequest<FactoryStorage>
 */
#[OA\Schema(schema: 'FactoryUpdateStorageRequest')]
final class UpdateStorageRequest extends UpdateRequest
{
    #[OA\Property]
    public Material $material;
    #[OA\Property]
    public int $quantity;
}
