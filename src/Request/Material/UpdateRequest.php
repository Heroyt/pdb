<?php

declare(strict_types=1);

namespace App\Request\Material;

use App\Models\Material;
use App\Request\Attributes\IntRange;
use OpenApi\Attributes as OA;

/**
 * @extends \App\Request\UpdateRequest<Material>
 */
#[OA\Schema(schema: "MaterialUpdateRequest")]
final class UpdateRequest extends \App\Request\UpdateRequest
{
    #[OA\Property(nullable: true)]
    public string $name;
    #[OA\Property(nullable: true), IntRange(min: 1)]
    public int $size;
    #[OA\Property(nullable: true)]
    public bool $wildcard;
}
