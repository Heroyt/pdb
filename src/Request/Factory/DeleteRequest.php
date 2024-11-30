<?php

declare(strict_types=1);

namespace App\Request\Factory;

use App\Models\Factory;
use App\Request\Attributes\ModelExists;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "FactoryDeleteRequest")]
final class DeleteRequest
{
    public function __construct(
        #[ModelExists(Factory::class), OA\Property]
        public int $id,
    ) {
    }
}
