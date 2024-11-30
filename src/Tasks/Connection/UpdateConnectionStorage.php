<?php

declare(strict_types=1);

namespace App\Tasks\Connection;

use App\Request\Connection\UpdateStorageRequest;
use App\Services\Provider\ConnectionProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<UpdateStorageRequest>
 */
final readonly class UpdateConnectionStorage implements TaskDispatcherInterface
{
    public function __construct(
        private ConnectionProvider $connectionProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.connection.storage.update';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof UpdateStorageRequest);

        $this->connectionProvider->updateStorage($payload);

        $task->ack();
    }
}
