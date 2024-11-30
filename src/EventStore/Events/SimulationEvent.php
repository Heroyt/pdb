<?php
declare(strict_types=1);

namespace App\EventStore\Events;

use App\EventStore\Event;

class SimulationEvent extends Event
{

    public function __construct(
      public int $step,
    ) {}

}