<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Command;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\TemplateCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheCommand extends Command
{
    private ShopAdapterInterface $shopAdapter;
    private TemplateCacheServiceInterface $templateCacheService;
    private ContainerCacheInterface $containerCache;

    public function __construct(
        ShopAdapterInterface $shopAdapter,
        TemplateCacheServiceInterface $templateCacheService,
        ContainerCacheInterface $containerCache
    ) {
        $this->shopAdapter = $shopAdapter;
        $this->templateCacheService = $templateCacheService;
        $this->containerCache = $containerCache;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Clears shop cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->templateCacheService->invalidateTemplateCache();
        $this->shopAdapter->invalidateModulesCache();
        $this->containerCache->invalidate();

        $output->writeln("<info>Cleared cache files</info>");

        return 0;
    }
}