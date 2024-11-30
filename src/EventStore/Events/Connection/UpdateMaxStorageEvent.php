<?php

declare(strict_types=1);

namespace App\EventStore\Events\Connection;

use App\EventStore\Event;

class UpdateMaxStorageEvent extends Event
{
    public function __construct(
        public readonly int $connectionId,
        public readonly int $materialId,
        public readonly int $maxQuantity,
    ) {
    }
}
