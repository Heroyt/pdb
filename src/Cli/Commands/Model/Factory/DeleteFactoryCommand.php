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
use Symfony\Component\Console\Output\OutputInterface;

class DeleteFactoryCommand extends Command
{
    public static function getDefaultName(): ?string {
        return 'model:factory:delete';
    }

    public static function getDefaultDescription(): ?string {
        return 'Delete an existing factory';
    }

    protected function configure(): void {
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $id = (int) $input->getArgument('id');

        try {
            $factory = Factory::get($id);
        } catch (ModelNotFoundException) {
            $output->writeln("<error>Factory with ID `$id` was not found.</error>");
            return self::FAILURE;
        }

        if (!$factory->delete()) {
            $output->writeln('<error>Failed to delete factory.</error>');
            return self::FAILURE;
        }


        $output->writeln('<info>Factory removed</info>');
        return self::SUCCESS;
    }
}
