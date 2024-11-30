<?php

declare(strict_types=1);

namespace App\Dto\Db;

use OpenApi\Attributes as OA;

#[OA\Schema]
class StoppedFactory
{
    #[OA\Property]
    public int $id_factory;
    #[OA\Property]
    public string $name;
    #[OA\Property]
    public int $storage_capacity;
    #[OA\Property]
    public float $stored;
    #[OA\Property]
    public float $out_size;
    #[OA\Property]
    public int $has_all_materials;
}
