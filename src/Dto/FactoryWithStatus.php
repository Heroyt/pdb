<?php

declare(strict_types=1);

namespace App\Dto;

use App\Dto\Db\FactoryStatusRow;
use App\Models\Factory;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'FactoryWithStatusDto')]
class FactoryWithStatus extends FactoryFull
{
    #[OA\Property(description: 'Total size of materials in storage', minimum: 0)]
    public int $stored;
    #[OA\Property(description: 'Total size of produces materials (minimum required space)', minimum: 0)]
    public int $outSize;
    #[OA\Property(description: 'If it has all required process inputs in storage')]
    public bool $hasAllMaterials;

    public static function fromFactoryStatusRow(FactoryStatusRow $row): self {
        $factory = Factory::get($row->id_factory);
        $self = static::fromFactory($factory);
        $self->stored = (int)$row->stored;
        $self->outSize = (int)$row->out_size;
        $self->hasAllMaterials = $row->has_all_materials === 1;
        return $self;
    }
}
