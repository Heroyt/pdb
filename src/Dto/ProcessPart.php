<?php
declare(strict_types=1);

namespace App\Dto;

use App\Models\Material;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "ProcessPartDto")]
readonly class ProcessPart
{

    public function __construct(
      #[OA\Property]
      public int $id,
      #[OA\Property]
      public Material $material,
      #[OA\Property]
      public int $quantity,
    ){}

    public static function fromProcess(\App\Models\Process $process): self {
        return new self(
          $process->id,
          $process->material,
          $process->quantity,
        );
    }

}