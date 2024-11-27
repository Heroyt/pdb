<?php

declare(strict_types=1);

namespace App\Cli\Commands\Model\Connection;

use App\Exceptions\ModelCreationException;
use App\Models\Factory;
use App\Services\Provider\ConnectionProvider;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateConnectionCommand extends Command
{
    public function __construct(
        private readonly ConnectionProvider $connectionProvider,
    ) {
        parent::__construct();
    }

    public static function getDefaultName(): ?string {
        return 'model:connection:create';
    }

    public static function getDefaultDescription(): ?string {
        return 'Create a new connection';
    }

    protected function configure(): void {
        $this->addArgument(
            'start',
            InputArgument::REQUIRED,
            'Start factory ID',
            suggestedValues: static function (CompletionInput $input) {
                $factories = Factory::query()
                                  ->where('CAST(id AS CHAR) LIKE %~like~', $input->getCompletionValue())
                                  ->get();
                return array_values(
                    array_map(
                        static fn(Factory $factory) => new Suggestion((string) $factory->id, $factory->name),
                        $factories
                    )
                );
            }
        );
        $this->addArgument(
            'end',
            InputArgument::REQUIRED,
            'End factory ID',
            suggestedValues: static function (CompletionInput $input) {
                $factories = Factory::query()
                                  ->where('CAST(id AS CHAR) LIKE %~like~', $input->getCompletionValue())
                                  ->get();
                return array_values(
                    array_map(
                        static fn(Factory $factory) => new Suggestion((string) $factory->id, $factory->name),
                        $factories
                    )
                );
            }
        );
        $this->addOption(
            'speed',
            's',
            InputOption::VALUE_REQUIRED,
            'Connection speed (positive integer)'
        );
        $this->addOption(
            'capacity',
            'c',
            InputOption::VALUE_REQUIRED,
            'Connection material capacity (positive integer)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $startId = (int) $input->getArgument('start');
        $endId = (int) $input->getArgument('end');

        try {
            $start = Factory::get($startId);
        } catch (ModelNotFoundException) {
            $output->writeln("<error>Factory with ID `$startId` was not found.</error>");
            return self::FAILURE;
        }
        try {
            $end = Factory::get($endId);
        } catch (ModelNotFoundException) {
            $output->writeln("<error>Factory with ID `$endId` was not found.</error>");
            return self::FAILURE;
        }

        $speed = max(1, (int) $input->getOption('speed'));
        $capacity = max(1, (int) $input->getOption('capacity'));

        try {
            $connection = $this->connectionProvider->createConnection($start, $end, $speed, $capacity);
        } catch (ModelCreationException) {
            $output->writeln('<error>Failed to create a connection.</error>');
            return self::FAILURE;
        }

        $output->writeln('<info>Connection (ID: ' . $connection->id . ') created successfully.</info>');
        return self::SUCCESS;
    }
}
