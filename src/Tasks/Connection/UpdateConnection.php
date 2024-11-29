<?php

declare(strict_types=1);

namespace App\Tasks\Connection;

use App\Request\Connection\UpdateRequest;
use App\Services\Provider\ConnectionProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<UpdateRequest>
 */
final readonly class UpdateConnection implements TaskDispatcherInterface
{
    public function __construct(
        private ConnectionProvider $connectionProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.connection.update';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof UpdateRequest);

        // Update the current state of entity
        $payload->entity->fetch(true);

        $this->connectionProvider->updateConnection($payload);
        $task->ack();
    }
}
