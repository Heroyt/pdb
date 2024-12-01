<?php

declare(strict_types=1);

namespace App\Tasks\Connection;

use App\Models\Connection;
use App\Request\Connection\UnassignRequest;
use App\Services\Provider\ConnectionProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Spiral\RoadRunner\Metrics\Metrics;
use Throwable;

/**
 * @implements TaskDispatcherInterface<UnassignRequest>
 */
final readonly class UnassignConnection implements TaskDispatcherInterface
{
    public function __construct(
      private ConnectionProvider $connectionProvider,
      private Metrics            $metrics,
    ) {}

    /**
     * @inheritDoc
     */
    public static function getDiName() : string {
        return 'task.connection.unassign';
    }

    public function process(ReceivedTaskInterface $task) : void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof UnassignRequest);

        try {
            $connection = Connection::get($payload->id);
            $wasAssigned = $connection->assigned;
            $this->connectionProvider->setAssigned($connection, false);
        } catch (Throwable $e) {
            $task->nack($e->getMessage());
            return;
        }
        if (!$wasAssigned) {
            $this->metrics->sub('assigned_connections', 1);
        }
        $task->ack();
    }
}
