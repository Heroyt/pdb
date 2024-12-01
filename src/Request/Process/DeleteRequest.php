<?php

declare(strict_types=1);

namespace App\Request\Process;

use App\Models\Process;
use App\Request\Attributes\ModelExists;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "ProcessDeleteRequest")]
final class DeleteRequest
{
    public function __construct(
        #[ModelExists(Process::class), OA\Property]
        public int $id,
    ) {
    }
}
