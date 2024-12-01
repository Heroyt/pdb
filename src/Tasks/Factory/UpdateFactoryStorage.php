<?php

declare(strict_types=1);

namespace App\Tasks\Factory;

use App\Enums\StorageUpdateType;
use App\Exceptions\ModelCreationException;
use App\Exceptions\ModelDeleteException;
use App\Request\Factory\UpdateStorageRequest;
use App\Services\Provider\FactoryProvider;
use App\Tasks\TaskDispatcherInterface;
use Dibi\DriverException;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Lsr\Core\Exceptions\ValidationException;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Spiral\RoadRunner\Metrics\MetricsInterface;

/**
 * @implements TaskDispatcherInterface<UpdateStorageRequest>
 */
final readonly class UpdateFactoryStorage implements TaskDispatcherInterface
{
    public function __construct(
        private FactoryProvider $factoryProvider,
      private MetricsInterface $metrics,
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

        try {
            $this->factoryProvider->updateStorage($payload);
        } catch (ModelCreationException | ModelDeleteException | DriverException | ModelNotFoundException | ValidationException $e) {
            $task->nack($e, true);
        }

        $metric = match($payload->type) {
            StorageUpdateType::PRODUCTION  => 'produced_materials',
            StorageUpdateType::CONSUMPTION => 'consumed_materials',
            StorageUpdateType::LOADING     => 'loaded_materials',
            StorageUpdateType::UNLOADING   => 'unloaded_materials',
            StorageUpdateType::OTHER       => null,
        };
        if ($metric !== null) {
            $this->metrics->add(
              $metric,
              abs($payload->quantity),
              [
                $payload->material->name,
                (string) $payload->material->id,
              ]
            );
        }

        $task->ack();
    }
}
