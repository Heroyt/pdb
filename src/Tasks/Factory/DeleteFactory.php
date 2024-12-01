<?php

declare(strict_types=1);

namespace App\Tasks\Factory;

use App\Models\Factory;
use App\Request\Factory\DeleteRequest;
use App\Services\Provider\FactoryProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Throwable;

/**
 * @implements TaskDispatcherInterface<DeleteRequest>
 */
final readonly class DeleteFactory implements TaskDispatcherInterface
{
    public function __construct(
      private FactoryProvider $factoryProvider,
    ) {}

    /**
     * @inheritDoc
     */
    public static function getDiName() : string {
        return 'task.factory.delete';
    }

    public function process(ReceivedTaskInterface $task) : void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof DeleteRequest);

        try {
            $factory = Factory::get($payload->id);
            $this->factoryProvider->deleteFactory($factory);
        } catch (Throwable $e) {
            $task->nack($e->getMessage());
            return;
        }
        $task->ack();
    }
}
