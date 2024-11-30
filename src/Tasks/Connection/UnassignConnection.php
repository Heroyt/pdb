<?php

declare(strict_types=1);

namespace App\Tasks\Connection;

use App\Models\Connection;
use App\Request\Connection\UnassignRequest;
use App\Services\Provider\ConnectionProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<UnassignRequest>
 */
final readonly class UnassignConnection implements TaskDispatcherInterface
{
    public function __construct(
        private ConnectionProvider $connectionProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.connection.unassign';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof UnassignRequest);

        $connection = Connection::get($payload->id);
        $this->connectionProvider->setAssigned($connection, false);
        $task->ack();
    }
}
