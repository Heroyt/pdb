<?php

namespace App\Cli\Commands\Logs;

use Lsr\Logging\Exceptions\ArchiveCreationException;
use Lsr\Logging\LogArchiver;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ArchiveLogsCommand extends Command
{
    public function __construct(
        private readonly LogArchiver $archiver,
    ) {
        parent::__construct('log:archive');
    }

    public static function getDefaultName(): ?string {
        return 'log:archive';
    }

    public static function getDefaultDescription(): string {
        return 'Archive old logs.';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $dirIt = new RecursiveDirectoryIterator(LOG_DIR);
        $itIt = new RecursiveIteratorIterator($dirIt, RecursiveIteratorIterator::LEAVES_ONLY);
        $it = new RegexIterator($itIt, '/.*-\\d{4}-\\d{2}-\\d{2}\\.log/');
        $processed = [];

        foreach ($it as $file) {
            $output->writeln('Checking ' . $file, $output::VERBOSITY_DEBUG);
            $path = pathinfo($file, PATHINFO_DIRNAME);
            $fileName = pathinfo($file, PATHINFO_BASENAME);
            preg_match('/^(.*)-\d{4}-\d{2}-\d{2}\.log$/', $fileName, $matches);
            $name = $matches[1] ?? '';
            if (empty($name) || isset($processed[$name])) {
                $output->writeln('Skipping ' . $name . ' (' . $fileName . ')', $output::VERBOSITY_DEBUG);
                continue;
            }
            try {
                $weeks = $this->archiver->archiveOld($path, $name, LOG_DIR . 'archive/');
                if ($weeks === null) {
                    $output->writeln(
                        '<comment>No logs to archive</comment>',
                        $output::VERBOSITY_DEBUG
                    );
                } else {
                    $output->writeln('Archived ' . $name);
                    $output->writeln(implode(', ', $weeks), $output::VERBOSITY_DEBUG);
                }
            } catch (ArchiveCreationException $e) {
                $output->writeln(
                    '<error>Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString() . '</error>'
                );
                return self::FAILURE;
            }
            $processed[$name] = true;
        }

        return self::SUCCESS;
    }
}
