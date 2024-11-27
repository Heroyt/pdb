<?php
declare(strict_types=1);

namespace App\EventStore\Events\Material;

use App\EventStore\Event;
use App\Models\Material;

class DeleteEvent extends Event
{
    public function __construct(
      public readonly int $id,
    ){}

    public static function fromMaterial(Material $material): self {
        return new self(
          $material->id,
        );
    }
}