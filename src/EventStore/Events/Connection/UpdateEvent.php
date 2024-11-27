<?php

declare(strict_types=1);

namespace App\EventStore\Events\Connection;

use App\EventStore\Event;
use App\Models\Connection;

class UpdateEvent extends Event
{
    public bool $assigned;
    public bool $active;
    public int $speed;
    public int $capacity;

    public function __construct(
        public readonly int $id,
    ) {
    }

    public static function fromConnection(
        Connection $connection,
    ): self {
        return new self(
            $connection->id,
        );
    }
}
