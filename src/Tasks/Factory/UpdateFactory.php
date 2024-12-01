<?php

declare(strict_types=1);

namespace App\Tasks\Factory;

use App\Request\Factory\UpdateRequest;
use App\Services\Provider\FactoryProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Throwable;

/**
 * @implements TaskDispatcherInterface<UpdateRequest>
 */
final readonly class UpdateFactory implements TaskDispatcherInterface
{
    public function __construct(
      private FactoryProvider $factoryProvider,
    ) {}

    /**
     * @inheritDoc
     */
    public static function getDiName() : string {
        return 'task.factory.update';
    }

    public function process(ReceivedTaskInterface $task) : void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof UpdateRequest);

        try {
            // Update the current state of entity
            $payload->entity->fetch(true);

            $this->factoryProvider->updateFactory($payload);
        } catch (Throwable $e) {
            $task->nack($e->getMessage());
            return;
        }
        $task->ack();
    }
}
