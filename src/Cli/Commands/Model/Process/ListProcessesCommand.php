<?php

declare(strict_types=1);

namespace App\Cli\Commands\Model\Process;

use App\Enums\Direction;
use App\Models\Factory;
use App\Models\Process;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListProcessesCommand extends Command
{
    public static function getDefaultName(): ?string {
        return 'model:process';
    }

    public static function getDefaultDescription(): ?string {
        return 'List processes for factory';
    }

    protected function configure(): void {
        $this->addArgument('factory', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Factory IDs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $factoryIds = $input->getArgument('factory');

        foreach ($factoryIds as $factoryId) {
            try {
                $factory = Factory::get((int) $factoryId);
            } catch (ModelNotFoundException) {
                $output->writeln("<error>Factory with ID `$factoryId` was not found.</error>");
                return self::FAILURE;
            }
            $processes = Process::query()->where('id_factory = %i', $factoryId)->get();

            if (count($processes) === 0) {
                $output->writeln("<comment>No process found.</comment>");
                return self::SUCCESS;
            }

            assert($output instanceof ConsoleOutputInterface);
            $section = $output->section();

            $io = new SymfonyStyle($input, $section);

            $inputsTable = $io->createTable();
            $inputsTable->setHeaderTitle('Inputs');
            $inputsTable->setHeaders(['ID', 'Material', 'Quantity']);

            $outputsTable = $io->createTable();
            $outputsTable->setHeaderTitle('Outputs');
            $outputsTable->setHeaders(['ID', 'Material', 'Quantity']);

            foreach ($processes as $process) {
                if ($process->type === Direction::IN) {
                    $inputsTable->addRow(
                        [
                        $process->id,
                        $process->material->name . ' (ID: ' . $process->material->id . ')',
                        $process->quantity,
                        ]
                    );
                } else {
                    $outputsTable->addRow(
                        [
                        $process->id,
                        $process->material->name . ' (ID: ' . $process->material->id . ')',
                        $process->quantity,
                        ]
                    );
                }
            }
            $io->section(
                sprintf('Process for factory: %s (ID: %d)', $factory->name, $factory->id)
            );
            $inputsTable->render();
            $section->writeln('');
            $outputsTable->render();
            $section->writeln('');
        }

        return self::SUCCESS;
    }
}
