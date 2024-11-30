<?php

declare(strict_types=1);

namespace App\Request\Connection;

use App\Models\Connection;
use App\Request\Attributes\ModelExists;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "ConnectionActivateRequest")]
final readonly class ActivateRequest
{
    public function __construct(
        #[ModelExists(Connection::class), OA\Property]
        public int $id,
    ) {
    }
}
