<?php

declare(strict_types=1);

namespace App\Tasks\Factory;

use App\Request\Factory\CreateRequest;
use App\Services\Provider\FactoryProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Throwable;

/**
 * @implements TaskDispatcherInterface<CreateRequest>
 */
final readonly class CreateFactory implements TaskDispatcherInterface
{
    public function __construct(
      private FactoryProvider $factoryProvider,
    ) {}

    /**
     * @inheritDoc
     */
    public static function getDiName() : string {
        return 'task.factory.create';
    }

    public function process(ReceivedTaskInterface $task) : void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof CreateRequest);

        try {
            $this->factoryProvider->createFactory($payload->name, $payload->capacity);
        } catch (Throwable $e) {
            $task->nack($e->getMessage());
            return;
        }
        $task->ack();
    }
}
