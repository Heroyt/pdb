<?php

declare(strict_types=1);

namespace App\Tasks\Process;

use App\Models\Material;
use App\Request\Process\CreateRequest;
use App\Services\Provider\ProcessProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Throwable;

/**
 * @implements TaskDispatcherInterface<CreateRequest>
 */
final readonly class CreateProcess implements TaskDispatcherInterface
{
    public function __construct(
      private ProcessProvider $processProvider,
    ) {}

    /**
     * @inheritDoc
     */
    public static function getDiName() : string {
        return 'task.process.create';
    }

    public function process(ReceivedTaskInterface $task) : void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof CreateRequest);

        try {
            $material = Material::get($payload->material);
            $this->processProvider->createProcess($payload->factory, $payload->type, $material, $payload->quantity);
        } catch (Throwable $e) {
            $task->nack($e->getMessage());
            return;
        }
        $task->ack();
    }
}
