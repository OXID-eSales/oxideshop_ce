<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer;

use OxidEsales\Eshop\Core\FileCache;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Internal\Framework\Database\BootstrapConnectionFactory;
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
    public function __construct(private readonly BasicContextInterface $context)
    {
    }

    public function getContainer(): SymfonyContainerBuilder
    {
        $symfonyContainer = new SymfonyContainerBuilder();
        $symfonyContainer->addCompilerPass(new RegisterListenersPass());
        $symfonyContainer->addCompilerPass(new AddConsoleCommandPass());
        $this->loadEditionServices($symfonyContainer);
        $this->loadModuleServices($symfonyContainer);
        $this->loadProjectServices($symfonyContainer);

        return $symfonyContainer;
    }

    private function loadProjectServices(SymfonyContainerBuilder $symfonyContainer): void
    {
        $loader = new YamlFileLoader($symfonyContainer, new FileLocator());
        try {
            $this->cleanupProjectYaml();
            $loader->load($this->context->getGeneratedServicesFilePath());
        } catch (FileLocatorFileNotFoundException) {
            // In case generated services file not found, do nothing.
        }
        try {
            $loader->load($this->context->getConfigurableServicesFilePath());
        } catch (FileLocatorFileNotFoundException) {
            // In case manually created services file not found, do nothing.
        }
        try {
            $loader->load($this->context->getShopConfigurableServicesFilePath($this->getShopId()));
        } catch (FileLocatorFileNotFoundException) {
            // In case manually created services file not found, do nothing.
        }
    }

    /**
     * Removes imports from modules that have deleted on the file system.
     */
    private function cleanupProjectYaml(): void
    {
        $projectYamlDao = new ProjectYamlDao($this->context, new Filesystem());
        $yamlImportService = new ProjectYamlImportService($projectYamlDao, $this->context);
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
                $this->context->getCommunityEditionSourcePath(),
            ],
            BasicContext::PROFESSIONAL_EDITION => [
                $this->context->getCommunityEditionSourcePath(),
                $this->context->getProfessionalEditionRootPath(),
            ],
            BasicContext::ENTERPRISE_EDITION => [
                $this->context->getCommunityEditionSourcePath(),
                $this->context->getProfessionalEditionRootPath(),
                $this->context->getEnterpriseEditionRootPath(),
            ],
        ];

        return $allEditionPaths[$this->context->getEdition()];
    }

    private function loadModuleServices(SymfonyContainerBuilder $symfonyContainer): void
    {
        $moduleServicesFilePath = $this->context->getActiveModuleServicesFilePath($this->getShopId());
        try {
            $loader = new YamlFileLoader($symfonyContainer, new FileLocator());
            $loader->load($moduleServicesFilePath);
        } catch (FileLocatorFileNotFoundException) {
            //no active modules, do nothing.
        } catch (LoaderLoadException $exception) {
            $loggerServiceFactory = new LoggerServiceFactory(new Context());
            $logger = $loggerServiceFactory->getLogger();
            // phpcs:disable
            $logger->error(
                "Can't load module services file path $moduleServicesFilePath. Please check if file exists and all imports in the file are correct.",
                [$exception]
            );
            // phpcs:enable
        }
    }

    private function getShopId(): int
    {
        return (int)(new ShopIdCalculator(
            new FileCache(),
            BootstrapConnectionFactory::create()
        ))->getShopId();
    }
}
