<?php

declare(strict_types=1);

namespace App\Cli\Commands;

use App\EventStore\Streams;
use App\Models\Factory;
use App\Services\Provider\PathFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Serializer;

class EventStoreTest extends Command
{
    public function __construct(
        private readonly Streams $streams,
        private readonly PathFinder $pathFinder,
        private readonly Serializer $serializer,
        ?string                  $name = null
    ) {
        parent::__construct($name);
    }

    public static function getDefaultName(): ?string {
        return 'eventstore:test';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->pathFinder->indexGraph(true);
        $output->writeln($this->serializer->serialize($this->pathFinder->findPaths(Factory::get(3), Factory::get(10)), 'json'));
//        $event = new TestEvent(uniqid());

//        var_dump($this->streams->appendEvent($event));

//        $read = $this->streams->query()
//          ->stream($this->streams::DEFAULT_STREAM)
//          ->type(TestEvent::class)
//            ->limit(10)
//          ->send();
//
//        foreach ($read->get() as $event) {
//            var_dump($event);
//        }

        return self::SUCCESS;
    }
}
