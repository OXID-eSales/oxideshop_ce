<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCacheInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheCommand extends Command
{
    public function __construct(
        private readonly ContainerCacheInterface $containerCache,
        private readonly ContextInterface $context,
        private readonly ShopCacheCleanerInterface $shopCacheCleaner,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Clears shop cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->shopCacheCleaner->clearAll();
        foreach ($this->context->getAllShopIds() as $shopId) {
            $this->containerCache->invalidate($shopId);
        }
        $output->writeln('<info>Cleared cache files</info>');

        return 0;
    }
}
