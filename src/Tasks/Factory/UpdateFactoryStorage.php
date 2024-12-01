<?php

declare(strict_types=1);

namespace App\Tasks\Factory;

use App\Enums\StorageUpdateType;
use App\Request\Factory\UpdateStorageRequest;
use App\Services\Provider\FactoryProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Spiral\RoadRunner\Metrics\MetricsInterface;
use Throwable;

/**
 * @implements TaskDispatcherInterface<UpdateStorageRequest>
 */
final readonly class UpdateFactoryStorage implements TaskDispatcherInterface
{
    public function __construct(
      private FactoryProvider  $factoryProvider,
      private MetricsInterface $metrics,
    ) {}

    /**
     * @inheritDoc
     */
    public static function getDiName() : string {
        return 'task.factory.storage.update';
    }

    public function process(ReceivedTaskInterface $task) : void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof UpdateStorageRequest);

        try {
            $this->factoryProvider->updateStorage($payload);
        } catch (Throwable $e) {
            $task->nack($e, true);
            return;
        }

        $metric = match ($payload->type) {
            StorageUpdateType::PRODUCTION  => 'produced_materials',
            StorageUpdateType::CONSUMPTION => 'consumed_materials',
            StorageUpdateType::LOADING     => 'loaded_materials',
            StorageUpdateType::UNLOADING   => 'unloaded_materials',
            StorageUpdateType::SELL        => 'sold_materials',
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
        if ($payload->type === StorageUpdateType::LOADING) {
            $this->metrics->add(
              'transported_materials',
              abs($payload->quantity),
              [
                $payload->material->name,
                (string) $payload->material->id,
              ]
            );
        }
        else if ($payload->type === StorageUpdateType::UNLOADING) {
            $this->metrics->sub(
              'transported_materials',
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
