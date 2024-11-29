<?php
declare(strict_types=1);

namespace App\Request\Material;

use App\Models\Material;
use App\Request\Attributes\ModelExists;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "MaterialDeleteRequest")]
final class DeleteRequest
{
    public function __construct(
      #[ModelExists(Material::class), OA\Property]
      public int $id,
    ) {}

}