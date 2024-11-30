<?php

declare(strict_types=1);

namespace App\Tasks\Factory;

use App\Exceptions\ModelCreationException;
use App\Exceptions\ModelDeleteException;
use App\Request\Factory\UpdateStorageRequest;
use App\Services\Provider\FactoryProvider;
use App\Tasks\TaskDispatcherInterface;
use Dibi\DriverException;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Lsr\Core\Exceptions\ValidationException;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<UpdateStorageRequest>
 */
final readonly class UpdateFactoryStorage implements TaskDispatcherInterface
{
    public function __construct(
        private FactoryProvider $factoryProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.factory.storage.update';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof UpdateStorageRequest);

        var_dump($payload->material->name, $payload->quantity);
        try {
            $this->factoryProvider->updateStorage($payload);
        } catch (ModelCreationException | ModelDeleteException | DriverException | ModelNotFoundException | ValidationException $e) {
            $task->nack($e, true);
        }

        $task->ack();
    }
}
