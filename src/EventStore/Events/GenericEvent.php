<?php

declare(strict_types=1);

namespace App\EventStore\Events;

use AllowDynamicProperties;
use App\EventStore\Event;

/**
 * Event object with no defined structure
 */
#[AllowDynamicProperties]
class GenericEvent extends Event
{
    public string $type;

    /**
     * @param  array<string,mixed>  $data
     * @param  string  $type
     * @return GenericEvent
     */
    public static function fromData(array $data, string $type): GenericEvent {
        $event = new self();
        $event->type = $type;
        foreach ($data as $key => $value) {
            $event->{$key} = $value;
        }
        return $event;
    }
}
