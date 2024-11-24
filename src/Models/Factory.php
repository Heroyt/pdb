<?php
declare(strict_types=1);

namespace App\Models;

use Lsr\Core\Models\Attributes\PrimaryKey;
use Lsr\Core\Models\Model;
use OpenApi\Attributes as OA;

#[PrimaryKey('id_factory'), OA\Schema]
class Factory extends Model
{

    public const string TABLE = 'factories';

    #[OA\Property]
    public string $name;
    #[OA\Property]
    public int $storageCapacity = 0;

}