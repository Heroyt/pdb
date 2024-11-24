<?php
declare(strict_types=1);

namespace App\Cli\Commands\Model\Factory;

use App\Models\Factory;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListFactoryCommand extends Command
{

    public static function getDefaultName() : ?string {
        return 'model:factory';
    }

    public static function getDefaultDescription() : ?string {
        return 'List factories';
    }

    protected function configure() : void {
        $this->addOption(
          'search',
          's',
          InputOption::VALUE_REQUIRED,
          'Search factory by its name.'
        );
        $this->addOption(
          'id',
          'i',
          InputOption::VALUE_REQUIRED,
          'Search factory by its ID.',
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
        $this->addOption(
          'limit',
          'l',
          InputOption::VALUE_REQUIRED,
          'Limit the number of returned factories.',
          50
        );
        $this->addOption(
          'offset',
          'o',
          InputOption::VALUE_REQUIRED,
          'Offset of the returned factories (in combination with limit).',
          0
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $io = new SymfonyStyle($input, $output);
        $search = $input->getOption('search');
        $id = $input->getOption('id');

        if (!empty($id)) {
            if (!is_numeric($id)) {
                $output->writeln('<error>Option `id` must be a positive integer.</error>');
                return self::FAILURE;
            }

            try {
                $factory = Factory::get((int) $id);
                $this->outputFactories($factory, $io);
            } catch (ModelNotFoundException) {
                $output->writeln("<error>Factory with ID `$id` was not found</error>");
                return self::FAILURE;
            }
            return self::SUCCESS;
        }

        $limit = (int) $input->getOption('limit');
        $offset = (int) $input->getOption('offset');

        $query = Factory::query()
                        ->limit($limit)
                        ->offset($offset);

        if (!empty($search)) {
            $query->where('name LIKE %~like~', $search);
        }

        $factories = $query->get();

        if (count($factories) === 0) {
            $output->writeln("<comment>No factories found.</comment>");
            return self::SUCCESS;
        }

        $output->writeln(sprintf('<info>Found %d factories</info>', count($factories)));
        $this->outputFactories($factories, $io);
        return self::SUCCESS;
    }

    /**
     * @param  Factory[]|Factory  $factories
     * @param  SymfonyStyle  $io
     * @return void
     */
    private function outputFactories(array | Factory $factories, SymfonyStyle $io) : void {
        $table = $io->createTable();
        $table->setHeaders(['ID', 'Name', 'Storage capacity']);

        if ($factories instanceof Factory) {
            $table->addRow([$factories->id, $factories->name, $factories->storageCapacity]);
        }
        else {
            foreach ($factories as $factory) {
                $table->addRow([$factory->id, $factory->name, $factory->storageCapacity]);
            }
        }
        $table->render();
    }

}