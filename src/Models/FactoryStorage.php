<?php

declare(strict_types=1);

namespace App\Models;

use Lsr\Core\Models\Attributes\ManyToOne;
use Lsr\Core\Models\Attributes\PrimaryKey;
use Lsr\Core\Models\Model;
use OpenApi\Attributes as OA;

#[PrimaryKey('id_storage'), OA\Schema]
class FactoryStorage extends Model
{
    public const string TABLE = 'factory_storage';

    #[ManyToOne(localKey: 'id_factory'), OA\Property]
    public Factory $facility;
    #[ManyToOne, OA\Property]
    public Material $material;
    #[OA\Property]
    public int $quantity;
}
