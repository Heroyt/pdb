<?php

declare(strict_types=1);

namespace App\EventStore\Events\Process;

use App\EventStore\Event;
use App\Models\Process;

class CreateEvent extends Event
{
    public function __construct(
        public readonly int $id,
        public readonly int $materialId,
        public readonly string $type,
        public readonly int $quantity,
    ) {
    }

    public static function fromProcess(Process $process): self {
        return new self(
          $process->id,
          $process->material->id,
          $process->type->value,
          $process->quantity,
        );
    }
}
