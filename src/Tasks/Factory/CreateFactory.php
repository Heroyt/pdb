<?php

declare(strict_types=1);

namespace App\Tasks\Factory;

use App\Services\FactoryProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

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
        assert($payload instanceof CreateFactoryPayload);

        $factory = $this->factoryProvider->createFactory($payload->name, $payload->capacity);
        $task->ack();
    }
}
