<?php

declare(strict_types=1);

namespace App\Cli\Commands\Model\Material;

use App\Exceptions\ModelCreationException;
use App\Services\Provider\MaterialProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMaterialCommand extends Command
{
    public function __construct(
        private readonly MaterialProvider $provider,
    ) {
        parent::__construct();
    }

    public static function getDefaultName(): ?string {
        return 'model:material:create';
    }

    public static function getDefaultDescription(): ?string {
        return 'Create a new material';
    }

    protected function configure(): void {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the material');
        $this->addArgument('size', InputArgument::OPTIONAL, 'Material size (must be a positive integer)', 1);
        $this->addOption(
            'wildcard',
            'w',
            InputOption::VALUE_NONE,
            'Is the material a wildcard (substitutes any material)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $name = $input->getArgument('name');
        $size = $input->getArgument('size');
        if (!is_numeric($size) || ((int) $size) < 1) {
            $output->writeln('<error>Argument `size` must be a positive integer.</error>');
            return self::FAILURE;
        }
        $size = (int) $size;
        try {
            $material = $this->provider->createMaterial($name, $size, $input->getOption('wildcard') ?? false);
        } catch (ModelCreationException) {
            $output->writeln('<error>Failed to create material.</error>');
            return self::FAILURE;
        }

        $output->writeln("<info>Created a new material with ID {$material->id}</info>");
        return self::SUCCESS;
    }
}
