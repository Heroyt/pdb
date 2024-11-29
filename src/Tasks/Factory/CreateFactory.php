<?php

declare(strict_types=1);

namespace App\Tasks\Factory;

use App\Request\Factory\CreateRequest;
use App\Services\Provider\FactoryProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<CreateRequest>
 */
class CreateFactory implements TaskDispatcherInterface
{
    public function __construct(
        private readonly FactoryProvider $factoryProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.factory.create';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof CreateRequest);

        $factory = $this->factoryProvider->createFactory($payload->name, $payload->capacity);
        $task->ack();
    }
}
