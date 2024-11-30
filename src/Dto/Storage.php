<?php

declare(strict_types=1);

namespace App\Dto;

use App\Models\FactoryStorage;
use App\Models\Material;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "StorageDto")]
readonly class Storage
{
    #[OA\Property]
    public int $size;

    public function __construct(
        #[OA\Property]
        public Material $material,
        #[OA\Property]
        public int $quantity,
    ) {
        $this->size = $material->size * $quantity;
    }

    public static function fromFactoryStorage(FactoryStorage $factoryStorage): self {
        return new self(
            $factoryStorage->material,
            $factoryStorage->quantity
        );
    }
}
