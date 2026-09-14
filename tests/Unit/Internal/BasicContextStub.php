<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal;

use OxidEsales\EshopCommunity\Internal\Container\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\Facts\Facts;
use Symfony\Component\Filesystem\Path;

/**
 * @internal
 */
class BasicContextStub implements BasicContextInterface
{
    private string $communityEditionSourcePath;
    private string $containerCacheFilePath;
    private string $edition;
    private string $enterpriseEditionRootPath;
    private string $generatedServicesFilePath;
    private string $configurableServicesFilePath;
    private string $shopConfigurableServicesFilePath;
    private string $professionalEditionRootPath;
    private string $sourcePath;
    private string $shopRootPath;
    private string $configFilePath;
    private string $projectConfigurationDirectory;
    private array $backwardsCompatibilityClassMap;
    private Facts $facts;
    private string $outPath;
    private string $vendorPath;
    private string $composerVendorName;
    private string $cacheDirectory;
    private string $moduleCacheDirectory;
    private string $templateCacheDirectory;
    private int $currentShopId;
    private string $activeModuleServicesFilePath;

    public function __construct()
    {
        /** @var BasicContextInterface $basicContext */
        $basicContext = BootstrapContainerFactory::getBootstrapContainer()->get(BasicContextInterface::class);

        $this->communityEditionSourcePath = $basicContext->getCommunityEditionSourcePath();
        $this->containerCacheFilePath = $basicContext->getContainerCacheFilePath($this->getDefaultShopId());
        $this->edition = $basicContext->getEdition();
        $this->enterpriseEditionRootPath = $basicContext->getEnterpriseEditionRootPath();
        $this->generatedServicesFilePath = $basicContext->getGeneratedServicesFilePath();
        $this->configurableServicesFilePath = $basicContext->getConfigurableServicesFilePath();
        $this->professionalEditionRootPath = $basicContext->getProfessionalEditionRootPath();
        $this->sourcePath = $basicContext->getSourcePath();
        $this->configFilePath = $basicContext->getConfigFilePath();
        $this->shopRootPath = $basicContext->getShopRootPath();
        $this->backwardsCompatibilityClassMap = $basicContext->getBackwardsCompatibilityClassMap();
        $this->facts = $basicContext->getFacts();
        $this->outPath = $basicContext->getOutPath();
        $this->vendorPath = $basicContext->getVendorPath();
        $this->composerVendorName = $basicContext->getComposerVendorName();
        $this->cacheDirectory = $basicContext->getCacheDirectory();
        $this->moduleCacheDirectory = $basicContext->getModuleCacheDirectory();
        $this->templateCacheDirectory = $basicContext->getTemplateCacheDirectory();
        $this->currentShopId = $basicContext->getCurrentShopId();
        $this->activeModuleServicesFilePath = $basicContext->getActiveModuleServicesFilePath($this->getCurrentShopId());
        $this->shopConfigurableServicesFilePath = $basicContext->getShopConfigurableServicesFilePath(
            $this->getCurrentShopId()
        );
    }

    public function getCommunityEditionSourcePath(): string
    {
        return $this->communityEditionSourcePath;
    }

    public function setCommunityEditionSourcePath(string $communityEditionSourcePath): void
    {
        $this->communityEditionSourcePath = $communityEditionSourcePath;
    }

    public function getContainerCacheFilePath(int $shopId): string
    {
        return $this->containerCacheFilePath;
    }

    public function getEdition(): string
    {
        return $this->edition;
    }

    public function setEdition(string $edition): void
    {
        $this->edition = $edition;
    }

    public function getEnterpriseEditionRootPath(): string
    {
        return $this->enterpriseEditionRootPath;
    }

    public function setEnterpriseEditionRootPath(string $enterpriseEditionRootPath): void
    {
        $this->enterpriseEditionRootPath = $enterpriseEditionRootPath;
    }

    public function getGeneratedServicesFilePath(): string
    {
        return $this->generatedServicesFilePath;
    }

    public function setGeneratedServicesFilePath(string $generatedServicesFilePath): void
    {
        $this->generatedServicesFilePath = $generatedServicesFilePath;
    }

    public function getConfigurableServicesFilePath(): string
    {
        return $this->configurableServicesFilePath;
    }

    public function setConfigurableServicesFilePath(string $configurableServicesFilePath): void
    {
        $this->configurableServicesFilePath = $configurableServicesFilePath;
    }

    public function getShopConfigurableServicesFilePath(int $shopId): string
    {
        return $this->shopConfigurableServicesFilePath;
    }

    public function setShopConfigurableServicesFilePath(string $shopConfigurableServicesFilePath): void
    {
        $this->shopConfigurableServicesFilePath = $shopConfigurableServicesFilePath;
    }

    public function getProfessionalEditionRootPath(): string
    {
        return $this->professionalEditionRootPath;
    }

    public function setProfessionalEditionRootPath(string $professionalEditionRootPath): void
    {
        $this->professionalEditionRootPath = $professionalEditionRootPath;
    }

    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }

    public function setSourcePath(string $sourcePath): void
    {
        $this->sourcePath = $sourcePath;
    }

    public function getDefaultShopId(): int
    {
        return 1;
    }

    public function getAllShopIds(): array
    {
        return [$this->getDefaultShopId()];
    }

    public function getFacts(): Facts
    {
        return $this->facts;
    }

    public function getBackwardsCompatibilityClassMap(): array
    {
        return $this->backwardsCompatibilityClassMap;
    }

    public function getProjectConfigurationDirectory(): string
    {
        return $this->projectConfigurationDirectory;
    }

    public function setProjectConfigurationDirectory(string $projectConfigurationDirectory): void
    {
        $this->projectConfigurationDirectory = $projectConfigurationDirectory;
    }

    public function getConfigFilePath(): string
    {
        return $this->configFilePath;
    }

    public function getConfigTableName(): string
    {
        return 'oxconfig';
    }

    public function getConfigurationDirectoryPath(): string
    {
        return $this->getSourcePath() . '/tmp/';
    }

    public function getShopRootPath(): string
    {
        return $this->shopRootPath;
    }

    public function getOutPath(): string
    {
        return $this->outPath;
    }

    public function getVendorPath(): string
    {
        return $this->vendorPath;
    }

    public function getComposerVendorName(): string
    {
        return $this->composerVendorName;
    }

    public function getCacheDirectory(): string
    {
        return $this->cacheDirectory;
    }

    public function setCacheDirectory(string $cacheDirectory): void
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    public function getModuleCacheDirectory(): string
    {
        return $this->moduleCacheDirectory;
    }

    public function getShopConfigurationDirectory(int $shopId): string
    {
        return Path::join($this->getProjectConfigurationDirectory(), 'shops', (string)$shopId);
    }

    public function getTemplateCacheDirectory(): string
    {
        return $this->templateCacheDirectory;
    }

    public function setTemplateCacheDirectory(string $templateCacheDirectory): void
    {
        $this->templateCacheDirectory = $templateCacheDirectory;
    }

    public function getActiveModuleServicesFilePath(int $shopId): string
    {
        return $this->activeModuleServicesFilePath;
    }

    public function setActiveModuleServicesFilePath(string $path): void
    {
        $this->activeModuleServicesFilePath = $path;
    }

    public function getCurrentShopId(): int
    {
        return $this->currentShopId;
    }

    public function setCurrentShopId(int $shopId): void
    {
        $this->currentShopId = $shopId;
    }
}
