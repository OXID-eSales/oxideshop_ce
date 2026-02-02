<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle\BundleContainerExtension;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle\BundleContainerExtensionInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle\BundleLoader;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\CompilerPass\MakeServicesPublicPass;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\CompilerPass\ViewControllerPass;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\CompilerPass\RoutePass;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ProjectYamlImportService;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\LoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Context;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Symfony\Component\Yaml\Yaml;

/**
 * @internal
 */
class ContainerBuilder
{
    /** @var BundleInterface[] */
    private array $bundles = [];

    private BundleContainerExtensionInterface $bundleContainerExtension;

    public function __construct(private BasicContextInterface $context)
    {
        $this->bundleContainerExtension = new BundleContainerExtension();
    }

    /**
     * @return SymfonyContainerBuilder
     * @throws \Exception
     */
    public function getContainer(): SymfonyContainerBuilder
    {
        $symfonyContainer = new SymfonyContainerBuilder();

        // Set kernel-compatible parameters for Symfony bundle compatibility
        $this->setKernelParameters($symfonyContainer);
        $this->registerSyntheticKernel($symfonyContainer);

        // OXID compiler passes (always registered)
        $symfonyContainer->addCompilerPass(new ViewControllerPass());
        $symfonyContainer->addCompilerPass(new RoutePass());
        $symfonyContainer->addCompilerPass(new MakeServicesPublicPass());

        // Initialize bundles (register extensions, call build())
        // Bundles may register their own compiler passes (e.g. FrameworkBundle adds RegisterListenersPass)
        $this->initializeBundles($symfonyContainer);

        // Register default passes only if no bundle provided them
        $this->addDefaultCompilerPasses($symfonyContainer);
        $this->setBundleParameters($symfonyContainer);
        $this->setMergePass($symfonyContainer);
        $this->loadBundleConfigs($symfonyContainer);

        // Load services: Edition → Module → Project
        // Bundle extension configs are loaded by MergeExtensionConfigurationPass during compilation
        $this->loadEditionServices($symfonyContainer);
        $this->loadModuleServices($symfonyContainer);
        $this->loadProjectServices($symfonyContainer);

        return $symfonyContainer;
    }

    /**
     * Set kernel-compatible parameters for Symfony bundle compatibility.
     * Many bundles expect these parameters to exist.
     */
    private function setKernelParameters(SymfonyContainerBuilder $container): void
    {
        $container->setParameter('kernel.project_dir', $this->context->getShopRootPath());
        $container->setParameter('kernel.environment', $this->getEnvironment());
        $container->setParameter('kernel.debug', $this->isDebug());
        $container->setParameter('kernel.charset', 'UTF-8');
        $container->setParameter('kernel.container_class', 'OxidContainer');
        $container->setParameter('kernel.cache_dir', $this->context->getCacheDirectory());
        $container->setParameter('kernel.build_dir', $this->context->getCacheDirectory());
        $container->setParameter('kernel.logs_dir', $this->context->getShopRootPath() . '/var/log');
        $container->setParameter('kernel.default_locale', 'en');
        $container->setParameter('kernel.secret', $_ENV['OXID_SECRET'] ?? '');

        $isCli = \PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg';
        $container->setParameter('kernel.runtime_mode', $isCli ? 'cli' : 'web');
        $container->setParameter('kernel.runtime_mode.web', !$isCli);
        $container->setParameter('kernel.runtime_mode.cli', $isCli);
        $container->setParameter('kernel.runtime_mode.worker', false);
        $container->setParameter('kernel.runtime_environment', $this->getEnvironment());
        $container->setParameter('container.build_id', hash('crc32', $this->context->getCacheDirectory()));
    }

    private function addDefaultCompilerPasses(SymfonyContainerBuilder $container): void
    {
        $passConfig = $container->getCompilerPassConfig();
        $existingPasses = array_merge(
            $passConfig->getBeforeOptimizationPasses(),
            $passConfig->getBeforeRemovingPasses(),
        );

        $hasPass = static fn(string $class) => array_filter(
            $existingPasses,
            static fn($pass) => $pass instanceof $class
        );

        if (!$hasPass(RegisterListenersPass::class)) {
            $container->addCompilerPass(new RegisterListenersPass());
        }

        if (!$hasPass(AddConsoleCommandPass::class)) {
            $container->addCompilerPass(new AddConsoleCommandPass());
        }
    }

    private function registerSyntheticKernel(SymfonyContainerBuilder $container): void
    {
        $container->register('kernel', KernelStub::class)
            ->setSynthetic(true)
            ->setPublic(true);
        $container->setAlias(KernelInterface::class, 'kernel')->setPublic(true);
    }

    /**
     * Determine the current environment.
     * Can be overridden via OXID_ENV environment variable.
     */
    private function getEnvironment(): string
    {
        return $_ENV['OXID_ENV'] ?? $_SERVER['OXID_ENV'] ?? 'prod';
    }

    /**
     * Determine if debug mode is enabled.
     * Can be overridden via OXID_DEBUG environment variable.
     */
    private function isDebug(): bool
    {
        if (isset($_ENV['OXID_DEBUG'])) {
            return filter_var($_ENV['OXID_DEBUG'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($_SERVER['OXID_DEBUG'])) {
            return filter_var($_SERVER['OXID_DEBUG'], FILTER_VALIDATE_BOOLEAN);
        }
        return $this->getEnvironment() !== 'prod';
    }

    /**
     * @return BundleInterface[]
     */
    public function getBundles(): array
    {
        return $this->bundles;
    }

    public function createKernelStub(): KernelStub
    {
        $bundleMap = [];
        foreach ($this->bundles as $bundle) {
            $bundleMap[$bundle->getName()] = $bundle;
        }

        return new KernelStub(
            environment: $this->getEnvironment(),
            debug: $this->isDebug(),
            projectDir: $this->context->getShopRootPath(),
            cacheDir: $this->context->getCacheDirectory(),
            logDir: $this->context->getShopRootPath() . '/var/log',
            bundles: $bundleMap,
            oxidContainerBuilder: $this,
        );
    }

    /**
     * Initialize bundles by loading them and registering with container.
     */
    private function initializeBundles(SymfonyContainerBuilder $symfonyContainer): void
    {
        $bundleLoader = new BundleLoader($this->context);
        $this->bundles = $bundleLoader->loadBundles($this->getEnvironment());

        $this->bundleContainerExtension->initializeBundles($symfonyContainer, $this->bundles);
    }

    private function setBundleParameters(SymfonyContainerBuilder $container): void
    {
        $bundleMap = [];
        $bundleMetadata = [];
        foreach ($this->bundles as $bundle) {
            $bundleMap[$bundle->getName()] = $bundle::class;
            $bundleMetadata[$bundle->getName()] = [
                'path'      => $bundle->getPath(),
                'namespace' => $bundle->getNamespace(),
            ];
        }
        $container->setParameter('kernel.bundles', $bundleMap);
        $container->setParameter('kernel.bundles_metadata', $bundleMetadata);
        $container->setParameter('oxid.bundle_classes', array_values(array_map(
            fn(BundleInterface $b) => $b::class,
            $this->bundles
        )));
    }

    private function setMergePass(SymfonyContainerBuilder $container): void
    {
        $extensions = array_keys($container->getExtensions());
        $container->getCompilerPassConfig()->setMergePass(
            new MergeExtensionConfigurationPass($extensions)
        );
    }

    private function loadBundleConfigs(SymfonyContainerBuilder $container): void
    {
        $configFile = $this->context->getShopRootPath() . '/var/configuration/framework.yaml';
        if (!file_exists($configFile)) {
            return;
        }

        $config = Yaml::parseFile($configFile);
        foreach ($config as $extensionAlias => $extensionConfig) {
            if ($container->hasExtension($extensionAlias)) {
                $container->loadFromExtension($extensionAlias, $extensionConfig ?? []);
            }
        }
    }

    /**
     * Loads a 'project.yaml' file if it can be found in the shop directory.
     *
     * @param SymfonyContainerBuilder $symfonyContainer
     * @throws \Exception
     */
    private function loadProjectServices(SymfonyContainerBuilder $symfonyContainer)
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
            $loader->load($this->context->getShopConfigurableServicesFilePath($this->context->getCurrentShopId()));
        } catch (FileLocatorFileNotFoundException) {
            // In case manually created services file not found, do nothing.
        }
    }

    /**
     * Removes imports from modules that have deleted on the file system.
     */
    private function cleanupProjectYaml()
    {
        $projectYamlDao = new ProjectYamlDao($this->context, new Filesystem());
        $yamlImportService = new ProjectYamlImportService($projectYamlDao, $this->context);
        $yamlImportService->removeNonExistingImports();
    }

    /**
     * @param SymfonyContainerBuilder $symfonyContainer
     * @throws \Exception
     */
    private function loadEditionServices(SymfonyContainerBuilder $symfonyContainer)
    {
        foreach ($this->getEditionsRootPaths() as $path) {
            $servicesLoader = new YamlFileLoader($symfonyContainer, new FileLocator($path));
            $servicesLoader->load('Internal/services.yaml');
        }
    }

    /**
     * @return array
     */
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
        $moduleServicesFilePath = $this->context->getActiveModuleServicesFilePath($this->context->getCurrentShopId());
        try {
            $loader = new YamlFileLoader($symfonyContainer, new FileLocator());
            $loader->load($moduleServicesFilePath);
        } catch (FileLocatorFileNotFoundException) {
            //no active modules, do nothing.
        } catch (LoaderLoadException $exception) {
            $loggerServiceFactory = new LoggerServiceFactory(new Context());
            $logger = $loggerServiceFactory->getLogger();
            $message = sprintf(
                "Can't load module services file path %s. "
                . 'Please check if file exists and all imports in the file are correct.',
                $moduleServicesFilePath
            );
            $logger->error($message, [$exception]);
        }
    }
}
