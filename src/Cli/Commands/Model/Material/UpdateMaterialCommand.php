<?php

declare(strict_types=1);

namespace App\Cli\Commands\Model\Material;

use App\Models\Material;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateMaterialCommand extends Command
{
    public static function getDefaultName() : ?string {
        return 'model:material:update';
    }

    public static function getDefaultDescription() : ?string {
        return 'Update an existing material';
    }

    protected function configure() : void {
        $this->addArgument('id', InputArgument::REQUIRED, 'Material ID');
        $this->addOption('name', '', InputOption::VALUE_REQUIRED, 'The name of the material');
        $this->addOption('size', '', InputOption::VALUE_REQUIRED, 'Size (must be a positive integer)');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $id = (int) $input->getArgument('id');

        try {
            $material = Material::get($id);
        } catch (ModelNotFoundException) {
            $output->writeln("<error>Material with ID `$id` was not found.</error>");
            return self::FAILURE;
        }

        $name = $input->getOption('name');
        $size = $input->getOption('size');

        if (empty($name) && empty($size)) {
            $output->writeln("<error>Specify a name or size to update.</error>");
            return self::FAILURE;
        }

        if (!empty($name)) {
            $material->name = $name;
        }

        if (!empty($size)) {
            if (!is_numeric($size) || ((int) $size) < 0) {
                $output->writeln('<error>Option `size` must be a positive integer.</error>');
                return self::FAILURE;
            }
            $material->size = (int) $size;
        }

        if (!$material->save()) {
            $output->writeln('<error>Failed to save material.</error>');
            return self::FAILURE;
        }


        $output->writeln('<info>Material saved</info>');
        return self::SUCCESS;
    }
}
