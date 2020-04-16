<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Command;

use OxidEsales\Eshop\Core\FileCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheCommand extends Command
{

    protected function configure(): void
    {
        $this->setDescription('Clears shop cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = FileCache::clearCache(true);
        $output->writeln("<info>Cleared {$count} cache files</info>");

        return 0;
    }
}
