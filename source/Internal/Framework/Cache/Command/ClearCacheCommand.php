<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Command;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\ShopTemplateCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheCommand extends Command
{
    public function __construct(
        private readonly ShopAdapterInterface $shopAdapter,
        private readonly ShopTemplateCacheServiceInterface $shopTemplateCacheService,
        private readonly ContainerCacheInterface $containerCache,
        private readonly ModuleCacheServiceInterface $moduleCacheService,
        private readonly ContextInterface $context
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Clears shop cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->shopTemplateCacheService->invalidateAllShopsCache();
        $this->shopAdapter->invalidateModulesCache();

        foreach ($this->context->getAllShopIds() as $shopId) {
            $this->containerCache->invalidate($shopId);
        }

        $this->moduleCacheService->invalidateAll();

        $output->writeln("<info>Cleared cache files</info>");

        return 0;
    }
}
