<?php
declare(strict_types=1);

namespace App\Cli\Commands\Model\Process;

use App\Enums\Direction;
use App\Models\Factory;
use App\Models\Material;
use App\Models\Process;
use Lsr\Core\DB;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateProcessCommand extends Command
{

    public static function getDefaultName() : ?string {
        return 'model:process:create';
    }

    public static function getDefaultDescription() : ?string {
        return 'Create a new process';
    }

    protected function configure() : void {
        $this->addArgument(
          'factory',
          InputArgument::REQUIRED,
          'Factory ID',
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
          'in',
          'i',
          InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
          'Material inputs (ID[:Quantity=1] format)'
        );
        $this->addOption(
          'out',
          'o',
          InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
          'Material outputs (ID[:Quantity=1] format)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $factoryId = (int) $input->getArgument('factory');

        try {
            $factory = Factory::get($factoryId);
        } catch (ModelNotFoundException) {
            $output->writeln("<error>Factory with ID `$factoryId` was not found.</error>");
            return self::FAILURE;
        }

        DB::getConnection()->begin();

        // Parse inputs
        $in = $input->getOption('in');
        $out = $input->getOption('out');

        $inProcesses = [];
        $outProcesses = [];

        foreach ($in as $key => $part) {
            $parts = explode(':', $part);
            $id = (int) $parts[0];
            $count = max(1, (int) ($parts[1] ?? 1));

            $process = new Process();
            $process->processFactory = $factory;
            $process->type = Direction::IN;
            $process->quantity = $count;
            try {
                $process->material = Material::get($id);
            } catch (ModelNotFoundException) {
                DB::getConnection()->rollback();
                $output->writeln("<error>Material (in: $key) with ID `$id` was not found.</error>");
                return self::FAILURE;
            }

            if (!$process->save()) {
                DB::getConnection()->rollback();
                $output->writeln("<error>Failed to create process (in: $key).</error>");
                return self::FAILURE;
            }
            $inProcesses[] = $process->id;
        }

        foreach ($out as $key => $part) {
            $parts = explode(':', $part);
            $id = (int) $parts[0];
            $count = max(1, (int) ($parts[1] ?? 1));

            $process = new Process();
            $process->processFactory = $factory;
            $process->type = Direction::OUT;
            $process->quantity = $count;
            try {
                $process->material = Material::get($id);
            } catch (ModelNotFoundException) {
                DB::getConnection()->rollback();
                $output->writeln("<error>Material (out: $key) with ID `$id` was not found.</error>");
                return self::FAILURE;
            }

            if (!$process->save()) {
                DB::getConnection()->rollback();
                $output->writeln("<error>Failed to create process (out: $key).</error>");
                return self::FAILURE;
            }
            $outProcesses[] = $process->id;
        }
        DB::getConnection()->commit();


        $output->writeln("<info>Created a new process with IDs</info>");
        $output->writeln('In: '.implode(', ', $inProcesses));
        $output->writeln('Out: '.implode(', ', $outProcesses));
        return self::SUCCESS;
    }

}