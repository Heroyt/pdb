<?php

declare(strict_types=1);

namespace App\EventStore\Events;

use App\EventStore\Event;

class TestEvent extends Event
{
    public function __construct(
        public string $value
    ) {
    }
}
