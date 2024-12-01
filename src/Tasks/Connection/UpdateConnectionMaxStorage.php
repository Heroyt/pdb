<?php

declare(strict_types=1);

namespace App\Tasks\Connection;

use App\Request\Connection\UpdateMaxStorageRequest;
use App\Services\Provider\ConnectionProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Throwable;

/**
 * @implements TaskDispatcherInterface<UpdateMaxStorageRequest>
 */
final readonly class UpdateConnectionMaxStorage implements TaskDispatcherInterface
{
    public function __construct(
      private ConnectionProvider $connectionProvider,
    ) {}

    /**
     * @inheritDoc
     */
    public static function getDiName() : string {
        return 'task.connection.storage.max';
    }

    public function process(ReceivedTaskInterface $task) : void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof UpdateMaxStorageRequest);

        try {
            $this->connectionProvider->updateMaxStorage($payload);
        } catch (Throwable $e) {
            $task->nack($e->getMessage());
            return;
        }

        $task->ack();
    }
}
