<?php

declare(strict_types=1);

namespace App\EventStore\Events\Connection;

use App\EventStore\Event;
use App\Models\Connection;

class DeleteEvent extends Event
{
    public function __construct(
        public readonly int $id,
    ) {
    }

    public static function fromConnection(Connection $connection): self {
        return new self(
            $connection->id,
        );
    }
}
