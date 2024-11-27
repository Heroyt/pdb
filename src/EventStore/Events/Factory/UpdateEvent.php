<?php
declare(strict_types=1);

namespace App\EventStore\Events\Factory;

use App\EventStore\Event;
use App\Models\Factory;

class UpdateEvent extends Event
{
      public string $name;
      public int $storageCapacity;
    public function __construct(
      public readonly int $id,
    ){}

    public static function fromFactory(Factory $factory): self {
        return new self(
          $factory->id,
        );
    }
}