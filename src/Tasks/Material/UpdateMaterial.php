<?php

declare(strict_types=1);

namespace App\Tasks\Material;

use App\Request\Material\UpdateRequest;
use App\Services\Provider\MaterialProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<UpdateRequest>
 */
final readonly class UpdateMaterial implements TaskDispatcherInterface
{
    public function __construct(
        private MaterialProvider $materialProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.material.update';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof UpdateRequest);

        // Update the current state of entity
        $payload->entity->fetch(true);

        $this->materialProvider->updateMaterial($payload);
        $task->ack();
    }
}
