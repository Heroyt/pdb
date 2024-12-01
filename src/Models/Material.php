<?php

declare(strict_types=1);

namespace App\Models;

use Lsr\Core\Models\Attributes\PrimaryKey;
use Lsr\Core\Models\Model;
use OpenApi\Attributes as OA;

#[PrimaryKey('id_material'), OA\Schema]
class Material extends Model
{
    public const string TABLE = 'materials';

    /** @var non-empty-string  */
    #[OA\Property]
    public string $name;
    #[OA\Property]
    public int $size;

    /** @var bool If true, accepts and material */
    #[OA\Property]
    public bool $wildcard = false;
}
