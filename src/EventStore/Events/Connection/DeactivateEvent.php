<?php

declare(strict_types=1);

namespace App\EventStore\Events\Connection;

use App\EventStore\Event;

class DeactivateEvent extends Event
{
    public function __construct(
        public readonly int $connectionId,
        public readonly bool $active = false,
    ) {
    }
}
