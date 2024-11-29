<?php
declare(strict_types=1);

namespace App\Dto\Db;

class StoppedFactory
{

    public int $id_factory;
    public string $name;
    public int $storage_capacity;
    public float $stored;
    public float $out_size;
    public int $has_all_materials;

}