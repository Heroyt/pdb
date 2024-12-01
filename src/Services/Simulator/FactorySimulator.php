<?php
declare(strict_types=1);

namespace App\Services\Simulator;

use App\Enums\Direction;
use App\Enums\StorageUpdateType;
use App\Request\Factory\UpdateStorageRequest;
use App\Services\Provider\FactoryProvider;
use App\Services\TaskProducer;
use App\Tasks\Factory\UpdateFactoryStorage;
use Symfony\Component\Console\Output\OutputInterface;

class FactorySimulator implements Simulator
{

    public function __construct(
      private readonly TaskProducer $taskProducer,
      private readonly FactoryProvider $factoryProvider,
    ) {}

    public function simulate(int $currentStep, OutputInterface $output) : void {
        $output->writeln('Simulating factory processes...', OutputInterface::VERBOSITY_VERBOSE);
        // Find all factories that can run their process.
        foreach($this->factoryProvider->findRunningFactories() as $factoryDto) {
            $factory = $factoryDto->getFactory();

            // Plan tasks for updating factory storage based on the set processes.
            foreach ($factory->processes as $process) {
                $storage = $factory->getOrCreateStorageForMaterial($process->material);
                $request = new UpdateStorageRequest($storage);
                $request->material = $process->material;
                // Inputs decrease the stored quantity, outputs increase quantity
                $request->quantity = $process->type === Direction::IN ? -$process->quantity : $process->quantity;
                $request->type = $process->type === Direction::IN ? StorageUpdateType::CONSUMPTION : StorageUpdateType::PRODUCTION;
                $output->writeln('Factory material '.$factory->name.' ('.$factory->id.') '.$request->quantity.' ('.$request->material->name.')', OutputInterface::VERBOSITY_VERBOSE);
                $this->taskProducer->plan(
                  UpdateFactoryStorage::class,
                  $request,
                );
            }
            $output->writeln('Simulated factory '.$factory->name.' ('.$factory->id.')', OutputInterface::VERBOSITY_VERBOSE);
        }
    }
}