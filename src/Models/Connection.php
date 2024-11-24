<?php

declare(strict_types=1);

namespace App\Models;

use Lsr\Core\Models\Attributes\PrimaryKey;
use Lsr\Core\Models\Model;
use OpenApi\Attributes as OA;

#[PrimaryKey('id_connection'), OA\Schema]
class Connection extends Model
{
    public const string TABLE = 'connections';

    #[OA\Property]
    public bool $assigned = false;
    #[OA\Property]
    public bool $active = false;
    #[OA\Property]
    public int $speed = 1;
    #[OA\Property]
    public int $storageCapacity = 10;
}
