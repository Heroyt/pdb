<?php

declare(strict_types=1);

namespace App\Cli\Commands\Model\Factory;

use App\Exceptions\ModelCreationException;
use App\Services\Provider\FactoryProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateFactoryCommand extends Command
{
    public function __construct(
        private readonly FactoryProvider $factoryProvider,
    ) {
        parent::__construct();
    }

    public static function getDefaultName(): ?string {
        return 'model:factory:create';
    }

    public static function getDefaultDescription(): ?string {
        return 'Create a new factory';
    }

    protected function configure(): void {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the factory');
        $this->addArgument(
            'storage_capacity',
            InputArgument::OPTIONAL,
            'Storage capacity (must be a positive integer)',
            50
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $name = $input->getArgument('name');
        $storageCapacity = $input->getArgument('storage_capacity');
        if (!is_numeric($storageCapacity) || ((int) $storageCapacity) < 1) {
            $output->writeln('<error>Argument `storage_capacity` must be a positive integer.</error>');
            return self::FAILURE;
        }
        $storageCapacity = (int) $storageCapacity;
        try {
            $factory = $this->factoryProvider->createFactory($name, $storageCapacity);
        } catch (ModelCreationException) {
            $output->writeln('<error>Failed to create factory.</error>');
            return self::FAILURE;
        }


        $output->writeln("<info>Created a new factory with ID {$factory->id}</info>");
        return self::SUCCESS;
    }
}
