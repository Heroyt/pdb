<?php
declare(strict_types=1);

namespace App\EventStore\Events\Material;

use App\EventStore\Event;
use App\Models\Material;

class CreateEvent extends Event
{
    public function __construct(
      public readonly int $id,
      public readonly string $name,
      public readonly int $size,
      public readonly bool $wildcard,
    ){}

    public static function fromMaterial(Material $material): self {
        return new self(
          $material->id,
          $material->name,
          $material->size,
          $material->wildcard,
        );
    }
}