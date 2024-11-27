<?php

declare(strict_types=1);

namespace App\Tasks\Factory;

use App\Request\Factory\UpdateRequest;
use App\Services\Provider\FactoryProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<UpdateRequest>
 */
class UpdateFactory implements TaskDispatcherInterface
{
    public function __construct(
        private readonly FactoryProvider $factoryProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.factory.update';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof UpdateRequest);

        // Update the current state of entity
        $payload->entity->fetch(true);

        $this->factoryProvider->updateFactory($payload);
        $task->ack();
    }
}
