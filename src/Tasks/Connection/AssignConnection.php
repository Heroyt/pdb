<?php

declare(strict_types=1);

namespace App\Tasks\Connection;

use App\Models\Connection;
use App\Request\Connection\AssignRequest;
use App\Services\Provider\ConnectionProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<AssignRequest>
 */
final readonly class AssignConnection implements TaskDispatcherInterface
{
    public function __construct(
        private ConnectionProvider $connectionProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.connection.assign';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof AssignRequest);

        $connection = Connection::get($payload->id);
        $this->connectionProvider->setAssigned($connection, true);
        $task->ack();
    }
}
