<?php

declare(strict_types=1);

namespace App\Request\Connection;

use App\Models\Connection;
use App\Models\Factory;
use App\Request\Attributes\IntRange;
use App\Request\Attributes\ModelExists;
use App\Request\CreateRequest as AbstractCreateRequest;
use OpenApi\Attributes as OA;

/**
 * @extends AbstractCreateRequest<Connection>
 */
#[OA\Schema(schema: "ConnectionCreateRequest")]
final class CreateRequest extends AbstractCreateRequest
{

    public Factory $start {
        get => Factory::get($this->startId);
        set(Factory $value) {
            $this->start = $value;
            $this->startId = $value->id;
        }
    }

    public Factory $end {
        get => Factory::get($this->endId);
        set(Factory $value) {
            $this->end = $value;
            $this->endId = $value->id;
        }
    }

    /** @var int<1,max> */
    #[ModelExists(Factory::class), OA\Property(description: 'Start factory ID')]
    public int $startId;
    /** @var int<1,max> */
    #[ModelExists(Factory::class), OA\Property(description: 'End factory ID')]
    public int $endId;
    /** @var int<1,max> */
    #[IntRange(min: 1), OA\Property(minimum: 1)]
    public int $speed;
    /** @var int<1,max> */
    #[IntRange(min: 1), OA\Property(minimum: 1)]
    public int $capacity;

}
