<?php
declare(strict_types=1);

namespace App\Services\Provider;

use App\Enums\Direction;
use App\EventStore\Events\Process\CreateEvent;
use App\EventStore\Events\Process\DeleteEvent;
use App\EventStore\Streams;
use App\Exceptions\ModelCreationException;
use App\Exceptions\ModelDeleteException;
use App\Models\Factory;
use App\Models\Material;
use App\Models\Process;
use Dibi\DriverException;
use Lsr\Core\DB;
use Lsr\Core\Exceptions\ValidationException;

readonly class ProcessProvider
{

    public function __construct(
      private Streams $streams,
    ){}

    /**
     * @param  int<1,max>  $quantity
     * @throws ModelCreationException
     * @throws ValidationException
     * @throws DriverException
     */
    public function createProcess(Factory $factory, Direction $type, Material $material, int $quantity) : Process {
        if ($material->wildcard) {
            if ($type !== Direction::IN) {
                throw new ValidationException('Wildcard material must only be used as an input to process');
            }
            $this->validateFactoryWildcardProcess($factory, $material);
        }
        $process = $factory->getOrCreateProcessForMaterial($material, $type);
        $process->quantity += $quantity;
        DB::getConnection()->begin();
        if (!$process->save()) {
            DB::getConnection()->rollback();
            throw new ModelCreationException('Failed to create process.');
        }

        $result = $this->streams->appendEvent(CreateEvent::fromProcess($process), Process::TABLE.'_'.$process->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelCreationException($result->status->details);
        }

        DB::getConnection()->commit();
        return $process;
    }

    /**
     * If factory has a wildcard input, it can be the only process in a factory.
     *
     * @throws ValidationException
     */
    public function validateFactoryWildcardProcess(Factory $factory, ?Material $material): void {
        if (count(array_filter($factory->processes, static fn(Process $process) => $process->material->id !== $material?->id)) > 0) {
            throw new ValidationException('Wildcard material input cannot be combined with any other process.');
        }
    }

    /**
     * @throws DriverException
     * @throws ModelDeleteException
     */
    public function deleteProcess(Process $process): void {
        DB::getConnection()->begin();
        if (!$process->delete()) {
            DB::getConnection()->rollback();
            throw new ModelDeleteException('Failed to delete process.');
        }
        $result = $this->streams->appendEvent(DeleteEvent::fromProcess($process), $process::TABLE . '_' . $process->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelDeleteException($result->status->details);
        }
        DB::getConnection()->commit();
    }
}