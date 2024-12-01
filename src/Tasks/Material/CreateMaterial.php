<?php

declare(strict_types=1);

namespace App\Tasks\Material;

use App\Request\Material\CreateRequest;
use App\Services\Provider\MaterialProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Throwable;

/**
 * @implements TaskDispatcherInterface<CreateRequest>
 */
final readonly class CreateMaterial implements TaskDispatcherInterface
{
    public function __construct(
      private MaterialProvider $materialProvider,
    ) {}

    /**
     * @inheritDoc
     */
    public static function getDiName() : string {
        return 'task.material.create';
    }

    public function process(ReceivedTaskInterface $task) : void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof CreateRequest);

        try {
            $this->materialProvider->createMaterial($payload->name, $payload->size, $payload->wildcard);
        } catch (Throwable $e) {
            $task->nack($e->getMessage());
            return;
        }
        $task->ack();
    }
}
