<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\Direction;
use Lsr\Core\Models\Attributes\ManyToOne;
use Lsr\Core\Models\Attributes\PrimaryKey;
use Lsr\Core\Models\Model;
use OpenApi\Attributes as OA;

#[PrimaryKey('id_process'), OA\Schema]
class Process extends Model
{

    public const string TABLE = 'processes';

    #[ManyToOne(localKey: 'id_factory', class: Factory::class), OA\Property]
    public Factory $processFactory;
    #[ManyToOne, OA\Property]
    public Material $material;

    #[OA\Property]
    public Direction $direction;
    #[OA\Property]
    public int $quantity;

}