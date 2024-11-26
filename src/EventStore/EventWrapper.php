<?php

declare(strict_types=1);

namespace App\EventStore;

readonly class EventWrapper
{
    public function __construct(
        public Event $event,
        public ?string $id,
        public int $revision,
        public int $commitPosition,
    ) {
    }
}
