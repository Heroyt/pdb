<?php

declare(strict_types=1);

namespace App\Models;

use Lsr\Core\Models\Attributes\ManyToOne;
use Lsr\Core\Models\Attributes\PrimaryKey;
use Lsr\Core\Models\Model;
use OpenApi\Attributes as OA;

#[PrimaryKey('id_storage'), OA\Schema]
class ConnectionStorage extends Model
{
    public const string TABLE = 'connection_storage';

    #[ManyToOne, OA\Property]
    public Connection $connection;
    #[ManyToOne, OA\Property]
    public Material $material;
    #[OA\Property]
    public int $maxQuantity = 0;
    #[OA\Property]
    public int $quantity = 0;
}
