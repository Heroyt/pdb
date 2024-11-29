<?php

declare(strict_types=1);

namespace App\Request\Factory;

use App\Models\Factory;
use App\Request\Attributes\IntRange;
use App\Request\CreateRequest as AbstractCreateRequest;
use OpenApi\Attributes as OA;

/**
 * @extends AbstractCreateRequest<Factory>
 */
#[OA\Schema(schema: "FactoryCreateRequest")]
final class CreateRequest extends AbstractCreateRequest
{

    /** @var non-empty-string */
    #[OA\Property]
    public string $name;
    /** @var int<1,max> */
    #[OA\Property(minimum: 1), IntRange(min: 1)]
    public int $capacity;
}
