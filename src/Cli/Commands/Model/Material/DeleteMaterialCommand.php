<?php

declare(strict_types=1);

namespace App\Cli\Commands\Model\Material;

use App\Exceptions\ModelDeleteException;
use App\Models\Material;
use App\Services\Provider\MaterialProvider;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteMaterialCommand extends Command
{
    public function __construct(
        private readonly MaterialProvider $provider,
    ) {
        parent::__construct();
    }
    public static function getDefaultName(): ?string {
        return 'model:material:delete';
    }

    public static function getDefaultDescription(): ?string {
        return 'Delete an existing material';
    }

    protected function configure(): void {
        $this->addArgument('id', InputArgument::REQUIRED, 'Material ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $id = (int) $input->getArgument('id');

        try {
            $material = Material::get($id);
        } catch (ModelNotFoundException) {
            $output->writeln("<error>Material with ID `$id` was not found.</error>");
            return self::FAILURE;
        }

        try {
            $this->provider->deleteMaterial($material);
        } catch (ModelDeleteException) {
            $output->writeln('<error>Failed to delete material.</error>');
            return self::FAILURE;
        }


        $output->writeln('<info>Material removed</info>');
        return self::SUCCESS;
    }
}
