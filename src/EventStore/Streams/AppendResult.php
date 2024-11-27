<?php

declare(strict_types=1);

namespace App\EventStore\Streams;

use App\EventStore\Status;
use GRPC\EventStore\Streams\AppendResp;

class AppendResult
{
    public bool $success {
        get => $this->response !== null && $this->response->hasSuccess();
    }

    public ?int $revision {
        get => $this->response?->getSuccess()?->getCurrentRevision();
    }

    public ?int $position {
        get => $this->response?->getSuccess()?->getPosition()?->getCommitPosition();
    }

    public function __construct(
        public readonly string       $uuid,
        public readonly string       $eventType,
        public readonly string       $streamName,
        /** @phpstan-ignore property.onlyWritten */
        private readonly ?AppendResp $response,
        public readonly Status       $status,
    ) {
    }
}
