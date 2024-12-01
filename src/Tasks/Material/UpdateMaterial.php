<?php

declare(strict_types=1);

namespace App\Tasks\Material;

use App\Request\Material\UpdateRequest;
use App\Services\Provider\MaterialProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Throwable;

/**
 * @implements TaskDispatcherInterface<UpdateRequest>
 */
final readonly class UpdateMaterial implements TaskDispatcherInterface
{
    public function __construct(
      private MaterialProvider $materialProvider,
    ) {}

    /**
     * @inheritDoc
     */
    public static function getDiName() : string {
        return 'task.material.update';
    }

    public function process(ReceivedTaskInterface $task) : void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof UpdateRequest);

        try {
            // Update the current state of entity
            $payload->entity->fetch(true);
            $this->materialProvider->updateMaterial($payload);
        } catch (Throwable $e) {
            $task->nack($e->getMessage());
            return;
        }
        $task->ack();
    }
}
