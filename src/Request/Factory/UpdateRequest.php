<?php
declare(strict_types=1);

namespace App\Request\Factory;

use App\Models\Factory;
use App\Request\Attributes\IntRange;
use OpenApi\Attributes as OA;

/**
 * @extends \App\Request\UpdateRequest<Factory>
 */
#[OA\Schema(schema: "FactoryUpdateRequest")]
class UpdateRequest extends \App\Request\UpdateRequest
{

    #[OA\Property(nullable: true)]
    public string $name;
    #[OA\Property(minimum: 1, nullable: true), IntRange(min: 1)]
    public int $storageCapacity;

    public function __construct(
      Factory $entity,
    ){
        parent::__construct($entity);
    }
}