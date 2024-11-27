<?php

namespace App\Tasks;

use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

/**
 * @template Payload
 */
interface TaskDispatcherInterface
{
    /**
     * @return non-empty-string
     */
    public static function getDiName(): string;

    public function process(ReceivedTaskInterface $task): void;
}
