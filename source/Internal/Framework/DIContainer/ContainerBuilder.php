<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer;

use OxidEsales\Eshop\Core\FileCache;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Internal\Framework\Env\EnvUrlFormatter;
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
use Symfony\Component\Filesystem\Path;

/**
 * @internal
 */
class ContainerBuilder
{
    private int $shopId;
    private SymfonyContainerBuilder $containerBuilder;

    public function __construct(
        private readonly BasicContextInterface $basicContext,
    ) {
    }

    public function getContainer(): SymfonyContainerBuilder
    {
        $this->shopId = (int)(new ShopIdCalculator(new FileCache()))->getShopId();
        $this->containerBuilder = new SymfonyContainerBuilder();

        $this->containerBuilder->setParameter('oxid_esales.current_shop_id', $this->shopId);
        $this->containerBuilder->setParameter('oxid_shop_source_directory', $this->basicContext->getSourcePath());
        $this->containerBuilder->setParameter('oxid_cache_directory', $this->basicContext->getCacheDirectory());

        $this->containerBuilder->addCompilerPass(new RegisterListenersPass());
        $this->containerBuilder->addCompilerPass(new AddConsoleCommandPass());

        $this->loadEditionServices();
        $this->loadComponentServices();
        $this->loadModuleServices();
        $this->loadProjectServices();
        $this->loadProjectSubshopServices();
        $this->loadEnvironmentServices();
        $this->loadSubshopEnvironmentServices();

        return $this->containerBuilder;
    }

    private function loadEditionServices(): void
    {
        foreach ($this->getEditionsRootPaths() as $editionPath) {
            $this->getYamlLoader([$editionPath])->load('Internal/services.yaml');
        }
    }

    private function getEditionsRootPaths(): array
    {
        return match ($this->basicContext->getEdition()) {
            BasicContext::COMMUNITY_EDITION => [
                $this->basicContext->getCommunityEditionSourcePath(),
            ],
            BasicContext::PROFESSIONAL_EDITION => [
                $this->basicContext->getCommunityEditionSourcePath(),
                $this->basicContext->getProfessionalEditionRootPath()
            ],
            BasicContext::ENTERPRISE_EDITION => [
                $this->basicContext->getCommunityEditionSourcePath(),
                $this->basicContext->getProfessionalEditionRootPath(),
                $this->basicContext->getEnterpriseEditionRootPath(),
            ],
        };
    }

    private function loadComponentServices(): void
    {
        $this->loadYamlIfExists($this->getYamlLoader([]), $this->basicContext->getGeneratedServicesFilePath());
    }

    private function loadModuleServices(): void
    {
        $moduleServicesFilePath = $this->basicContext->getActiveModuleServicesFilePath($this->shopId);
        try {
            $this->loadYamlIfExists($this->getYamlLoader([]), $moduleServicesFilePath);
        } catch (LoaderLoadException $exception) {
            (new LoggerServiceFactory(new Context($this->shopId)))
                ->getLogger()
                ->error(
                    "Can't load module services file path $moduleServicesFilePath. "
                    . 'Please check if all imports in the file are correct.',
                    [$exception]
                );
        }
    }

    private function loadProjectServices(): void
    {
        $this->loadProjectExtensionFiles(
            $this->basicContext->getProjectConfigurationDirectory()
        );
    }

    private function loadProjectSubshopServices(): void
    {
        $this->loadProjectExtensionFiles(
            $this->basicContext->getShopConfigurationDirectory($this->shopId)
        );
    }

    private function loadSubshopEnvironmentServices(): void
    {
        $this->loadProjectExtensionFiles(
            $this->getShopConfigurationPathForSpecificEnvironment()
        );
    }

    private function getShopConfigurationPathForSpecificEnvironment(): string
    {
        return Path::join(
            EnvUrlFormatter::toEnvUrl(
                $this->basicContext->getProjectConfigurationDirectory()
            ),
            Path::makeRelative(
                $this->basicContext->getShopConfigurationDirectory($this->shopId),
                $this->basicContext->getProjectConfigurationDirectory()
            )
        );
    }

    private function loadEnvironmentServices(): void
    {
        $this->loadProjectExtensionFiles(
            EnvUrlFormatter::toEnvUrl(
                $this->basicContext->getProjectConfigurationDirectory()
            )
        );
    }

    private function loadProjectExtensionFiles(string $configurationUrl): void
    {
        foreach (['services.yaml', 'parameters.yaml'] as $file) {
            $this->loadYamlIfExists(
                $this->getYamlLoader([]),
                Path::join($configurationUrl, $file)
            );
        }
    }

    private function getYamlLoader(array $paths): YamlFileLoader
    {
        return new YamlFileLoader($this->containerBuilder, new FileLocator($paths));
    }

    private function loadYamlIfExists(YamlFileLoader $loader, string $yamlFile): void
    {
        try {
            $loader->load($yamlFile);
        } catch (FileLocatorFileNotFoundException) {
        }
    }
}
