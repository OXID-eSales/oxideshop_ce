<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer;

use OxidEsales\Eshop\Core\FileCache;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ProjectYamlImportService;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\LoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Context;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class ContainerBuilder
{
    public function __construct(private readonly BasicContextInterface $basicContext)
    {
    }

    public function getContainer(): SymfonyContainerBuilder
    {
        $shopId = $this->getCurrentShopId();
        $symfonyContainer = new SymfonyContainerBuilder();
        $symfonyContainer->setParameter('oxid_esales.current_shop_id', $shopId);
        $symfonyContainer->setParameter('oxid_shop_source_directory', $this->basicContext->getSourcePath());
        $symfonyContainer->addCompilerPass(new RegisterListenersPass());
        $symfonyContainer->addCompilerPass(new AddConsoleCommandPass());

        $symfonyContainer->setParameter('oxid_cache_directory', $this->basicContext->getCacheDirectory());
        $symfonyContainer->setParameter('oxid_shop_source_directory', $this->basicContext->getSourcePath());

        $this->loadEditionServices($symfonyContainer);
        $this->loadModuleServices($symfonyContainer, $shopId);
        $this->loadProjectServices($symfonyContainer, $shopId);

        return $symfonyContainer;
    }

    private function loadProjectServices(SymfonyContainerBuilder $symfonyContainer, int $shopId): void
    {
        $loader = new YamlFileLoader($symfonyContainer, new FileLocator());
        try {
            $this->cleanupProjectYaml();
            $loader->load($this->basicContext->getGeneratedServicesFilePath());
        } catch (FileLocatorFileNotFoundException) {
            // In case generated services file not found, do nothing.
        }
        try {
            $loader->load($this->basicContext->getConfigurableServicesFilePath());
        } catch (FileLocatorFileNotFoundException) {
            // In case manually created services file not found, do nothing.
        }
        try {
            $loader->load($this->basicContext->getShopConfigurableServicesFilePath($shopId));
        } catch (FileLocatorFileNotFoundException) {
            // In case manually created services file not found, do nothing.
        }
    }

    /**
     * Removes imports from modules that have deleted on the file system.
     */
    private function cleanupProjectYaml(): void
    {
        $projectYamlDao = new ProjectYamlDao($this->basicContext, new Filesystem());
        $yamlImportService = new ProjectYamlImportService($projectYamlDao, $this->basicContext);
        $yamlImportService->removeNonExistingImports();
    }

    private function loadEditionServices(SymfonyContainerBuilder $symfonyContainer): void
    {
        foreach ($this->getEditionsRootPaths() as $path) {
            $servicesLoader = new YamlFileLoader($symfonyContainer, new FileLocator($path));
            $servicesLoader->load('Internal/services.yaml');
        }
    }

    private function getEditionsRootPaths(): array
    {
        $allEditionPaths = [
            BasicContext::COMMUNITY_EDITION => [
                $this->basicContext->getCommunityEditionSourcePath(),
            ],
            BasicContext::PROFESSIONAL_EDITION => [
                $this->basicContext->getCommunityEditionSourcePath(),
                $this->basicContext->getProfessionalEditionRootPath(),
            ],
            BasicContext::ENTERPRISE_EDITION => [
                $this->basicContext->getCommunityEditionSourcePath(),
                $this->basicContext->getProfessionalEditionRootPath(),
                $this->basicContext->getEnterpriseEditionRootPath(),
            ],
        ];

        return $allEditionPaths[$this->basicContext->getEdition()];
    }

    private function loadModuleServices(SymfonyContainerBuilder $symfonyContainer, int $shopId): void
    {
        $moduleServicesFilePath = $this->basicContext->getActiveModuleServicesFilePath($shopId);
        try {
            $loader = new YamlFileLoader($symfonyContainer, new FileLocator());
            $loader->load($moduleServicesFilePath);
        } catch (FileLocatorFileNotFoundException) {
            //no active modules, do nothing.
        } catch (LoaderLoadException $exception) {
            (new LoggerServiceFactory(new Context($shopId)))
                ->getLogger()
                ->error(
                    "Can't load module services file path $moduleServicesFilePath. "
                    . 'Please check if file exists and all imports in the file are correct.',
                    [$exception]
                );
        }
    }

    private function getCurrentShopId(): int
    {
        return (int)(new ShopIdCalculator(new FileCache()))->getShopId();
    }
}
