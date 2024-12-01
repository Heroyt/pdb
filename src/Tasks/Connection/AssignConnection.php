<?php

declare(strict_types=1);

namespace App\Tasks\Connection;

use App\Models\Connection;
use App\Request\Connection\AssignRequest;
use App\Services\Provider\ConnectionProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Spiral\RoadRunner\Metrics\Metrics;
use Throwable;

/**
 * @implements TaskDispatcherInterface<AssignRequest>
 */
final readonly class AssignConnection implements TaskDispatcherInterface
{
    public function __construct(
      private ConnectionProvider $connectionProvider,
      private Metrics            $metrics,
    ) {}

    /**
     * @inheritDoc
     */
    public static function getDiName() : string {
        return 'task.connection.assign';
    }

    public function process(ReceivedTaskInterface $task) : void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof AssignRequest);

        $connection = Connection::get($payload->id);
        $wasAssigned = $connection->assigned;
        try {
            $this->connectionProvider->setAssigned($connection, true);
        } catch (Throwable $e) {
            $task->nack($e->getMessage());
            return;
        }
        if (!$wasAssigned) {
            $this->metrics->add('assigned_connections', 1);
        }
        $task->ack();
    }
}
