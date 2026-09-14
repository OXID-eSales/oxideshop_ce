<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Command;

use Symfony\Component\Cache\PruneableInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PruneCommand extends Command
{
    public function __construct(private readonly PruneableInterface $cachePool)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Deletes expired rate limiter buckets');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->cachePool->prune();

        (new SymfonyStyle($input, $output))->success('Pruned expired rate limiter buckets');

        return Command::SUCCESS;
    }
}
