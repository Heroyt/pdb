<?php

declare(strict_types=1);

namespace App\Tasks\Connection;

use App\Models\Connection;
use App\Request\Connection\DeactivateRequest;
use App\Services\Provider\ConnectionProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Spiral\RoadRunner\Metrics\Metrics;

/**
 * @implements TaskDispatcherInterface<DeactivateRequest>
 */
final readonly class DeactivateConnection implements TaskDispatcherInterface
{
    public function __construct(
      private ConnectionProvider $connectionProvider,
      private Metrics            $metrics,
    ) {}

    /**
     * @inheritDoc
     */
    public static function getDiName() : string {
        return 'task.connection.deactivate';
    }

    public function process(ReceivedTaskInterface $task) : void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof DeactivateRequest);

        $connection = Connection::get($payload->id);
        $wasActive = $connection->active;
        $this->connectionProvider->setActive($connection, false);
        if ($wasActive) {
            $this->metrics->sub('active_connections', 1);
        }
        $task->ack();
    }
}
