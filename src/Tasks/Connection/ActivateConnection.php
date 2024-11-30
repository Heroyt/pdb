<?php

declare(strict_types=1);

namespace App\Tasks\Connection;

use App\Models\Connection;
use App\Request\Connection\ActivateRequest;
use App\Services\Provider\ConnectionProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<ActivateRequest>
 */
final readonly class ActivateConnection implements TaskDispatcherInterface
{
    public function __construct(
        private ConnectionProvider $connectionProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.connection.activate';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof ActivateRequest);

        $connection = Connection::get($payload->id);
        $this->connectionProvider->setActive($connection, true);
        $task->ack();
    }
}
