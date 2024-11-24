<?php

namespace App\Cli\Commands\Cache;

use Lsr\Core\Caching\Cache;
use Lsr\Core\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CleanCacheCommand extends Command
{
    public function __construct(private readonly Cache $cache,) {
        parent::__construct('cache:clean');
    }

    public static function getDefaultName(): ?string {
        return 'cache:clean';
    }

    public static function getDefaultDescription(): ?string {
        return 'Clean server cache';
    }

    protected function configure(): void {
        $this->addOption(
            'all',
            'a',
            InputOption::VALUE_NONE,
            'Clear all cache.'
        );
        $this->addOption(
            'system',
            's',
            InputOption::VALUE_NONE,
            'Clear system cache (Redis).'
        );
        $this->addOption(
            'di',
            'd',
            InputOption::VALUE_NONE,
            'Clear DI cache.'
        );
        $this->addOption(
            'latte',
            'l',
            InputOption::VALUE_NONE,
            'Clear latte cache.'
        );
        $this->addOption(
            'model',
            'm',
            InputOption::VALUE_NONE,
            'Clear model (ORM) cache.'
        );
        $this->addOption(
            'info',
            'i',
            InputOption::VALUE_NONE,
            'Clear info cache.'
        );
        $this->addOption(
            'results',
            'r',
            InputOption::VALUE_NONE,
            'Clear results cache.'
        );
        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_NONE,
            'Clear config cache.'
        );
        $this->addOption(
            'tag',
            't',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'If set, only the records with specified tags will be removed.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $all = $input->getOption('all');
        $system = $input->getOption('system');
        $di = $input->getOption('di');
        $latte = $input->getOption('latte');
        $model = $input->getOption('model');
        $info = $input->getOption('info');
        $config = $input->getOption('config');
        $results = $input->getOption('results');

        if ($all || $system) {
            $tags = $input->getOption('tag');
            if (is_array($tags) && count($tags) > 0) {
                $this->cache->clean([\Nette\Caching\Cache::Tags => $tags]);
            } else {
                $this->cache->clean([\Nette\Caching\Cache::All => true]);
            }
            $output->writeln('<info>Successfully purged system cache</info>');
        }

        if ($all || $di) {
            $filesDi = glob(TMP_DIR . 'di/*');
            if ($filesDi === false) {
                $filesDi = [];
            }
            $filesPhp = glob(TMP_DIR . '*.php');
            if ($filesPhp === false) {
                $filesPhp = [];
            }
            $filesLock = glob(TMP_DIR . '*.php.lock');
            if ($filesLock === false) {
                $filesLock = [];
            }
            $files = array_merge(
                $filesDi,
                $filesPhp,
                $filesLock,
            );
            foreach ($files as $file) {
                unlink($file);
            }
            $output->writeln(
                sprintf(
                    '<info>Successfully removed %d DI cache files.</info>',
                    count($files)
                )
            );
        }

        if ($all || $latte) {
            $files = glob(TMP_DIR . 'latte/*');
            if ($files === false) {
                $files = [];
            }
            foreach ($files as $file) {
                unlink($file);
            }
            $output->writeln(
                sprintf(
                    '<info>Successfully removed %d latte cache files.</info>',
                    count($files)
                )
            );
        }

        if ($all || $model) {
            $files = glob(TMP_DIR . 'models/*');
            if ($files === false) {
                $files = [];
            }
            foreach ($files as $file) {
                unlink($file);
            }
            $output->writeln(
                sprintf(
                    '<info>Successfully removed %d model (ORM) cache files.</info>',
                    count($files)
                )
            );
        }

        if ($all || $info) {
            $output->writeln(
                '<info>Cleared info values.</info>'
            );
        }

        if ($all || $config) {
            Config::getInstance()->clearCache();
            $output->writeln(
                '<info>Cleared config cache.</info>'
            );
        }

        if ($all || $results) {
            $files = glob(TMP_DIR . 'results/*');
            if ($files === false) {
                $files = [];
            }
            foreach ($files as $file) {
                unlink($file);
            }
            $output->writeln(
                sprintf(
                    '<info>Successfully removed %d results cache files.</info>',
                    count($files)
                )
            );
        }


        return self::SUCCESS;
    }
}
