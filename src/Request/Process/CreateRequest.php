<?php

declare(strict_types=1);

namespace App\Request\Process;

use App\Enums\Direction;
use App\Models\Factory;
use App\Models\Material;
use App\Models\Process;
use App\Request\Attributes\IntRange;
use App\Request\Attributes\ModelExists;
use App\Request\CreateRequest as AbstractCreateRequest;
use Lsr\Core\Models\Attributes\Validation\Required;
use OpenApi\Attributes as OA;

/**
 * @extends AbstractCreateRequest<Process>
 */
#[OA\Schema(schema: "ProcessCreateRequest")]
final class CreateRequest extends AbstractCreateRequest
{
    public Factory $factory;
    #[OA\Property(description: 'Material ID', minimum: 1), Required, ModelExists(Material::class)]
    public int $material;
    /** @var int<1,max> */
    #[OA\Property(minimum: 1), IntRange(min: 1)]
    public int $quantity = 1;
    #[OA\Property, Required]
    public Direction $type;
}
