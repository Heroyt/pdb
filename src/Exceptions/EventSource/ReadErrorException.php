<?php

declare(strict_types=1);

namespace App\Exceptions\EventSource;

use App\EventStore\Status;

class ReadErrorException extends \RuntimeException
{
    /** @var array<string, string> */
    public readonly array $metadata;

    public function __construct(Status $status) {
        parent::__construct(
            $status->details,
            $status->code,
        );
        $this->metadata = $status->metadata;
    }
}
