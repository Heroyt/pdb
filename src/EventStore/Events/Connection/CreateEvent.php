<?php

declare(strict_types=1);

namespace App\EventStore\Events\Connection;

use App\EventStore\Event;
use App\Models\Connection;
use App\Models\Factory;

class CreateEvent extends Event
{
    public function __construct(
        public readonly int $id,
        public readonly bool $assigned,
        public readonly bool $active,
        public readonly int $speed,
        public readonly int $capacity,
        public readonly int $startId,
        public readonly int $endId,
    ) {
    }

    public static function fromConnection(
        Connection $connection,
        Factory $start,
        Factory $end,
    ): self {
        return new self(
            $connection->id,
            $connection->assigned,
            $connection->active,
            $connection->speed,
            $connection->storageCapacity,
            $start->id,
            $end->id,
        );
    }
}
