<?php

declare(strict_types=1);

namespace App\Tasks\Connection;

use App\Request\Connection\CreateRequest;
use App\Services\Provider\ConnectionProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Throwable;

/**
 * @implements TaskDispatcherInterface<CreateRequest>
 */
final readonly class CreateConnection implements TaskDispatcherInterface
{
    public function __construct(
      private ConnectionProvider $connectionProvider,
    ) {}

    /**
     * @inheritDoc
     */
    public static function getDiName() : string {
        return 'task.connection.create';
    }

    public function process(ReceivedTaskInterface $task) : void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof CreateRequest);

        try {
            $factory = $this->connectionProvider->createConnection(
              $payload->start,
              $payload->end,
              $payload->speed,
              $payload->capacity
            );
        } catch (Throwable $e) {
            $task->nack($e->getMessage());
            return;
        }
        $task->ack();
    }
}
