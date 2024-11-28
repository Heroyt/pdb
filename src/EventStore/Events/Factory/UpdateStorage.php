<?php
declare(strict_types=1);

namespace App\EventStore\Events\Factory;

use App\EventStore\Event;

class UpdateStorage extends Event
{

    public function __construct(
      public readonly int $factoryId,
      public readonly int $materialId,
      public readonly int $quantity,
    ){}

}