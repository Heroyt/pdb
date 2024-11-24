<?php
declare(strict_types=1);

namespace App\Cli\Commands\Model\Process;

use App\Models\Process;
use Lsr\Core\DB;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteProcessCommand extends Command
{

    public static function getDefaultName() : ?string {
        return 'model:process:delete';
    }

    public static function getDefaultDescription() : ?string {
        return 'Delete an existing process';
    }

    protected function configure() : void {
        $this->addOption('id', '', InputOption::VALUE_REQUIRED, 'Process ID');
        $this->addOption('factory', 'f', InputOption::VALUE_REQUIRED, 'Factory ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $id = $input->getOption('id');
        $factoryId = $input->getOption('factory');

        if ($id === null && $factoryId === null) {
            $output->writeln('You must provide an process ID or factory ID');
            return self::FAILURE;
        }

        if ($id !== null) {
            try {
                $process = Process::get((int) $id);
            } catch (ModelNotFoundException) {
                $output->writeln("<error>Process with ID `$id` was not found.</error>");
                return self::FAILURE;
            }

            if (!$process->delete()) {
                $output->writeln('<error>Failed to delete process.</error>');
                return self::FAILURE;
            }

            $output->writeln('<info>Process removed</info>');
            return self::SUCCESS;
        }

        DB::getConnection()->begin();
        $processes = Process::query()->where('id_factory = %i', $factoryId)->get();
        foreach ($processes as $process) {
            if (!$process->delete()) {
                DB::getConnection()->rollback();
                $output->writeln('<error>Failed to delete process (ID: '.$process->id.').</error>');
                return self::FAILURE;
            }
        }
        DB::getConnection()->commit();

        $output->writeln(sprintf('<info>Removed %d processes</info>', count($processes)));
        return self::SUCCESS;
    }

}