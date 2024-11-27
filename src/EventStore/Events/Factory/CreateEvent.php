<?php
declare(strict_types=1);

namespace App\EventStore\Events\Factory;

use App\EventStore\Event;
use App\Models\Factory;

class CreateEvent extends Event
{
    public function __construct(
      public readonly int $id,
      public readonly string $name,
      public readonly int $storageCapacity,
    ){}

    public static function fromFactory(Factory $factory): self {
        return new self(
          $factory->id,
          $factory->name,
          $factory->storageCapacity
        );
    }
}