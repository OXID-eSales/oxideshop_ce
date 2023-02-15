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
use Webmozart\PathUtil\Path;

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
    }

    /**
     * @return string
     */
    public function getCommunityEditionSourcePath(): string
    {
        return $this->communityEditionSourcePath;
    }

    /**
     * @param string $communityEditionSourcePath
     */
    public function setCommunityEditionSourcePath(string $communityEditionSourcePath): void
    {
        $this->communityEditionSourcePath = $communityEditionSourcePath;
    }

    /**
     * @return string
     */
    public function getContainerCacheFilePath(int $shopId): string
    {
        return $this->containerCacheFilePath;
    }

    /**
     * @return string
     */
    public function getEdition(): string
    {
        return $this->edition;
    }

    /**
     * @param string $edition
     */
    public function setEdition(string $edition): void
    {
        $this->edition = $edition;
    }

    /**
     * @return string
     */
    public function getEnterpriseEditionRootPath(): string
    {
        return $this->enterpriseEditionRootPath;
    }

    /**
     * @param string $enterpriseEditionRootPath
     */
    public function setEnterpriseEditionRootPath(string $enterpriseEditionRootPath): void
    {
        $this->enterpriseEditionRootPath = $enterpriseEditionRootPath;
    }

    /**
     * @return string
     */
    public function getGeneratedServicesFilePath(): string
    {
        return $this->generatedServicesFilePath;
    }

    /**
     * @param string $generatedServicesFilePath
     */
    public function setGeneratedServicesFilePath(string $generatedServicesFilePath): void
    {
        $this->generatedServicesFilePath = $generatedServicesFilePath;
    }

    /**
     * @return string
     */
    public function getConfigurableServicesFilePath(): string
    {
        return $this->configurableServicesFilePath;
    }

    /**
     * @param string $configurableServicesFilePath
     */
    public function setConfigurableServicesFilePath(string $configurableServicesFilePath): void
    {
        $this->configurableServicesFilePath = $configurableServicesFilePath;
    }

    /**
     * @return string
     */
    public function getProfessionalEditionRootPath(): string
    {
        return $this->professionalEditionRootPath;
    }

    /**
     * @param string $professionalEditionRootPath
     */
    public function setProfessionalEditionRootPath(string $professionalEditionRootPath): void
    {
        $this->professionalEditionRootPath = $professionalEditionRootPath;
    }

    /**
     * @return string
     */
    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }

    /**
     * @param string $sourcePath
     */
    public function setSourcePath(string $sourcePath): void
    {
        $this->sourcePath = $sourcePath;
    }

    /**
     * @return int
     */
    public function getDefaultShopId(): int
    {
        return 1;
    }

    /**
     * @return array
     */
    public function getAllShopIds(): array
    {
        return [$this->getDefaultShopId()];
    }

    /**
     * @return Facts
     */
    public function getFacts(): Facts
    {
        return $this->facts;
    }

    /**
     * @return array
     */
    public function getBackwardsCompatibilityClassMap(): array
    {
        return $this->backwardsCompatibilityClassMap;
    }

    /**
     * @return string
     */
    public function getProjectConfigurationDirectory(): string
    {
        return $this->projectConfigurationDirectory;
    }

    /**
     * @param string $projectConfigurationDirectory
     */
    public function setProjectConfigurationDirectory(string $projectConfigurationDirectory): void
    {
        $this->projectConfigurationDirectory = $projectConfigurationDirectory;
    }

    /**
     * @return string
     */
    public function getConfigFilePath(): string
    {
        return $this->configFilePath;
    }

    /**
     * @return string
     */
    public function getConfigTableName(): string
    {
        return 'oxconfig';
    }

    /**
     * @return string
     */
    public function getConfigurationDirectoryPath(): string
    {
        return $this->getSourcePath() . '/tmp/';
    }

    /**
     * @return string
     */
    public function getShopRootPath(): string
    {
        return $this->shopRootPath;
    }

    /**
     * @return string
     */
    public function getOutPath(): string
    {
        return $this->outPath;
    }

    /**
     * @return string
     */
    public function getVendorPath(): string
    {
        return $this->vendorPath;
    }

    /**
     * @return string
     */
    public function getComposerVendorName(): string
    {
        return $this->composerVendorName;
    }

    /**
     * @return string
     */
    public function getCacheDirectory(): string
    {
        return $this->cacheDirectory;
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
