<?php

declare(strict_types=1);

namespace App\Tasks\Process;

use App\Models\Process;
use App\Request\Process\DeleteRequest;
use App\Services\Provider\ProcessProvider;
use App\Tasks\TaskDispatcherInterface;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @implements TaskDispatcherInterface<DeleteRequest>
 */
final readonly class DeleteProcess implements TaskDispatcherInterface
{
    public function __construct(
        private ProcessProvider $materialProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDiName(): string {
        return 'task.process.delete';
    }

    public function process(ReceivedTaskInterface $task): void {
        $payload = igbinary_unserialize($task->getPayload());
        assert($payload instanceof DeleteRequest);

        $process = Process::get($payload->id);
        $this->materialProvider->deleteProcess($process);
        $task->ack();
    }
}
