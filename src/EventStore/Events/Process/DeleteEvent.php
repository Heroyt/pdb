<?php

declare(strict_types=1);

namespace App\EventStore\Events\Process;

use App\EventStore\Event;
use App\Models\Process;

class DeleteEvent extends Event
{
    public function __construct(
        public readonly int $id,
    ) {
    }

    public static function fromProcess(Process $process): self {
        return new self(
          $process->id,
        );
    }
}
