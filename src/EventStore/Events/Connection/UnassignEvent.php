<?php

declare(strict_types=1);

namespace App\EventStore\Events\Connection;

use App\EventStore\Event;

class UnassignEvent extends Event
{
    public function __construct(
        public readonly int $connectionId,
        public readonly bool $assigned = false,
    ) {
    }
}
