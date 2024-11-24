<?php
declare(strict_types=1);

namespace App\Cli\Commands\Model\Material;

use App\Models\Material;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListMaterialCommand extends Command
{

    public static function getDefaultName() : ?string {
        return 'model:material';
    }

    public static function getDefaultDescription() : ?string {
        return 'List materials';
    }

    protected function configure() : void {
        $this->addOption(
          'search',
          's',
          InputOption::VALUE_REQUIRED,
          'Search material by its name.'
        );
        $this->addOption(
          'id',
          'i',
          InputOption::VALUE_REQUIRED,
          'Search material by its ID.'
        );
        $this->addOption(
          'limit',
          'l',
          InputOption::VALUE_REQUIRED,
          'Limit the number of returned materials.',
          50
        );
        $this->addOption(
          'offset',
          'o',
          InputOption::VALUE_REQUIRED,
          'Offset of the returned materials (in combination with limit).',
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
                $material = Material::get((int) $id);
                $this->outputFactories($material, $io);
            } catch (ModelNotFoundException) {
                $output->writeln("<error>Material with ID `$id` was not found</error>");
                return self::FAILURE;
            }
            return self::SUCCESS;
        }

        $limit = (int) $input->getOption('limit');
        $offset = (int) $input->getOption('offset');

        $query = Material::query()
                         ->limit($limit)
                         ->offset($offset);

        if (!empty($search)) {
            $query->where('name LIKE %~like~', $search);
        }

        $materials = $query->get();

        if (count($materials) === 0) {
            $output->writeln("<comment>No materials found.</comment>");
            return self::SUCCESS;
        }

        $output->writeln(sprintf('<info>Found %d materials</info>', count($materials)));
        $this->outputFactories($materials, $io);
        return self::SUCCESS;
    }

    /**
     * @param  Material[]|Material  $materials
     * @param  SymfonyStyle  $io
     * @return void
     */
    private function outputFactories(array | Material $materials, SymfonyStyle $io) : void {
        $table = $io->createTable();
        $table->setHeaders(['ID', 'Name', 'Size']);

        if ($materials instanceof Material) {
            $table->addRow([$materials->id, $materials->name, $materials->size]);
        }
        else {
            foreach ($materials as $material) {
                $table->addRow([$material->id, $material->name, $material->size]);
            }
        }
        $table->render();
    }

}