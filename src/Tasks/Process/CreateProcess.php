<?php

declare(strict_types=1);

namespace App\Tasks\Process;

use App\Models\Material;
use App\Request\Process\CreateRequest;
use App\Services\Provider\ProcessProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<CreateRequest>
 */
final readonly class CreateProcess implements TaskDispatcherInterface
{
    public function __construct(
        private ProcessProvider $processProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.process.create';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof CreateRequest);

        $material = Material::get($payload->material);
        $process = $this->processProvider->createProcess($payload->factory, $payload->type, $material, $payload->quantity);
        $task->ack();
    }
}
