<?php

declare(strict_types=1);

namespace App\Tasks\Connection;

use App\Models\Connection;
use App\Request\Connection\DeleteRequest;
use App\Services\Provider\ConnectionProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<DeleteRequest>
 */
final readonly class DeleteConnection implements TaskDispatcherInterface
{
    public function __construct(
        private ConnectionProvider $connectionProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.connection.delete';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof DeleteRequest);

        $connection = Connection::get($payload->id);
        $this->connectionProvider->deleteConnection($connection);
        $task->ack();
    }
}
