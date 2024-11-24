<?php
declare(strict_types=1);

namespace App\Cli\Commands\Model\Factory;

use App\Models\Factory;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateFactoryCommand extends Command
{

    public static function getDefaultName() : ?string {
        return 'model:factory:update';
    }

    public static function getDefaultDescription() : ?string {
        return 'Update an existing factory';
    }

    protected function configure() : void {
        $this->addArgument(
          'id',
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
          },
        );
        $this->addOption('name', '', InputOption::VALUE_REQUIRED, 'The name of the factory');
        $this->addOption('capacity', '', InputOption::VALUE_REQUIRED, 'Storage capacity (must be a positive integer)');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $id = (int) $input->getArgument('id');

        try {
            $factory = Factory::get($id);
        } catch (ModelNotFoundException) {
            $output->writeln("<error>Factory with ID `$id` was not found.</error>");
            return self::FAILURE;
        }

        $name = $input->getOption('name');
        $capacity = $input->getOption('capacity');

        if (empty($name) && empty($capacity)) {
            $output->writeln("<error>Specify a name or capacity to update.</error>");
            return self::FAILURE;
        }

        if (!empty($name)) {
            $factory->name = $name;
        }

        if (!empty($capacity)) {
            if (!is_numeric($capacity) || ((int) $capacity) < 0) {
                $output->writeln('<error>Option `capacity` must be a positive integer.</error>');
                return self::FAILURE;
            }
            $factory->storageCapacity = (int) $capacity;
        }

        if (!$factory->save()) {
            $output->writeln('<error>Failed to save factory.</error>');
            return self::FAILURE;
        }


        $output->writeln('<info>Factory saved</info>');
        return self::SUCCESS;
    }

}