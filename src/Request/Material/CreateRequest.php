<?php

declare(strict_types=1);

namespace App\Request\Material;

use App\Models\Material;
use App\Request\Attributes\IntRange;
use App\Request\CreateRequest as AbstractCreateRequest;
use Lsr\Core\Models\Attributes\Validation\Required;
use OpenApi\Attributes as OA;

/**
 * @extends AbstractCreateRequest<Material>
 */
#[OA\Schema(schema: "MaterialCreateRequest")]
final class CreateRequest extends AbstractCreateRequest
{
    /** @var non-empty-string */
    #[OA\Property, Required]
    public string $name;
    /** @var int<1,max> */
    #[OA\Property(minimum: 1), IntRange(min: 1)]
    public int $size = 50;
    #[OA\Property(description: 'Used in processes where concrete materials do not matter')]
    public bool $wildcard = false;
}
