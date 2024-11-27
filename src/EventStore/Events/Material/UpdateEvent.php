<?php
declare(strict_types=1);

namespace App\EventStore\Events\Material;

use App\EventStore\Event;
use App\Models\Material;

class UpdateEvent extends Event
{
    public string $name;
    public int $size;
    public bool $wildcard;

    public function __construct(
      public readonly int $id,
    ) {}

    public static function fromMaterial(Material $material) : self {
        return new self(
          $material->id,
        );
    }
}