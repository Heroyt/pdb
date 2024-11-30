<?php
declare(strict_types=1);

namespace App\Cli\Commands\Simulator;

use App\EventStore\Events\SimulationEvent;
use App\EventStore\Streams;
use App\Services\Simulator\ConnectionSimulator;
use App\Services\Simulator\FactorySimulator;
use App\Services\TaskProducer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;

class SimulateCommand extends Command
{
    public function __construct(
      private readonly Streams $streams,
      private readonly LockFactory $lockFactory,
      private readonly TaskProducer $taskProducer,
      private readonly FactorySimulator $factorySimulator,
      private readonly ConnectionSimulator $connectionSimulator,
    ) {
        parent::__construct();
    }

    public static function getDefaultName(): ?string {
        return 'simulator:simulate';
    }

    public static function getDefaultDescription(): ?string {
        return 'Simulate one step of the whole system.';
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $output->writeln('Starting simulation...');

        // Make sure only one simulation is running at one time
        $lock = $this->lockFactory->createLock('simulation');
        $output->writeln('Acquiring simulation lock', OutputInterface::VERBOSITY_VERBOSE);
        if (!$lock->acquire(true)) {
            $output->writeln('<error>Failed to acquire simulation lock</error>');
            return self::FAILURE;
        }

        $lastStep = 0;
        /** @var Streams\ReadResult<SimulationEvent> $events */
        $events = $this->streams->query()
            ->type(SimulationEvent::class)
            ->limit(1)
            ->backwards()
            ->send();
        foreach ($events->get() as $eventWrapper) {
            /** @var SimulationEvent $event */
            $event = $eventWrapper->event;
            $lastStep = $event->step;
        }
        $step = $lastStep+1;
        $this->factorySimulator->simulate($step, $output);
        $this->connectionSimulator->simulate($step, $output);

        // Dispatch all planned tasks
        $this->streams->appendEvent(new SimulationEvent($step));
        $this->taskProducer->dispatch();
        $lock->release();
        $output->writeln(sprintf('<info>Simulation step (%d) complete</info>', $step));
        return self::SUCCESS;
    }

}