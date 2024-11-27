<?php

declare(strict_types=1);

namespace App\Tasks\Factory;

use App\Models\Factory;
use App\Services\Provider\FactoryProvider;
use App\Tasks\IdPayload;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<IdPayload>
 */
class DeleteFactory implements TaskDispatcherInterface
{
    public function __construct(
        private readonly FactoryProvider $factoryProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.factory.delete';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof IdPayload);

        $factory = Factory::get($payload->id);
        $this->factoryProvider->deleteFactory($factory);
        $task->ack();
    }
}
