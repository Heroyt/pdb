<?php

declare(strict_types=1);

namespace App\Tasks\Material;

use App\Models\Material;
use App\Request\Material\DeleteRequest;
use App\Services\Provider\MaterialProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<DeleteRequest>
 */
final readonly class DeleteMaterial implements TaskDispatcherInterface
{
    public function __construct(
        private MaterialProvider $materialProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.material.delete';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof DeleteRequest);

        $factory = Material::get($payload->id);
        $this->materialProvider->deleteMaterial($factory);
        $task->ack();
    }
}
