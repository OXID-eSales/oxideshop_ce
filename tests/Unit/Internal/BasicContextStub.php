<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal;

use OxidEsales\EshopCommunity\Internal\Container\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\Edition;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

class BasicContextStub implements BasicContextInterface
{
    private string $ceSourcePath;
    private string $peSourcePath;
    private string $eeSourcePath;
    private string $containerCacheFilePath;
    private Edition $edition;
    private string $generatedServicesFilePath;
    private string $sourcePath;
    private string $shopRootPath;
    private string $projectConfigurationDirectory;
    private array $backwardsCompatibilityClassMap;
    private string $outPath;
    private string $vendorPath;
    private string $composerVendorName;
    private string $cacheDirectory;
    private string $moduleCacheDirectory;
    private string $databaseUrl;
    protected string $activeModuleServicesFilePath;
    protected string $shopConfigurableServicesFilePath;
    private string $shopBaseUrl;

    public function __construct()
    {
        /** @var BasicContextInterface $basicContext */
        $basicContext = BootstrapContainerFactory::getBootstrapContainer()->get(BasicContextInterface::class);

        $this->containerCacheFilePath = $basicContext->getContainerCacheFilePath($this->getDefaultShopId());
        $this->edition = $basicContext->getEdition();
        $this->generatedServicesFilePath = $basicContext->getGeneratedServicesFilePath();
        $this->sourcePath = $basicContext->getSourcePath();
        $this->shopRootPath = $basicContext->getShopRootPath();
        $this->backwardsCompatibilityClassMap = $basicContext->getBackwardsCompatibilityClassMap();
        $this->outPath = $basicContext->getOutPath();
        $this->vendorPath = $basicContext->getVendorPath();
        $this->composerVendorName = $basicContext->getComposerVendorName();
        $this->cacheDirectory = $basicContext->getCacheDirectory();
        $this->moduleCacheDirectory = $basicContext->getModuleCacheDirectory();
        $this->activeModuleServicesFilePath = $basicContext->getActiveModuleServicesFilePath($this->getDefaultShopId());
        $this->databaseUrl = $basicContext->getDatabaseUrl();
        $this->projectConfigurationDirectory = $basicContext->getProjectConfigurationDirectory();
        $this->shopBaseUrl = $basicContext->getShopBaseUrl();
    }

    public function getContainerCacheFilePath(int $shopId): string
    {
        return $this->containerCacheFilePath;
    }

    public function getEdition(): Edition
    {
        return $this->edition;
    }

    public function setEdition(Edition $edition): void
    {
        $this->edition = $edition;
    }

    public function getGeneratedServicesFilePath(): string
    {
        return $this->generatedServicesFilePath;
    }

    public function setGeneratedServicesFilePath(string $generatedServicesFilePath): void
    {
        $this->generatedServicesFilePath = $generatedServicesFilePath;
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

    public function getConfigTableName(): string
    {
        return 'oxconfig';
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

    public function setVendorPath(string $path): void
    {
        $this->vendorPath = $path;
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

    public function getActiveModuleServicesFilePath(int $shopId): string
    {
        return $this->activeModuleServicesFilePath;
    }

    public function setActiveModuleServicesFilePath(string $path): void
    {
        $this->activeModuleServicesFilePath = $path;
    }

    public function getDatabaseUrl(): string
    {
        return $this->databaseUrl;
    }

    public function setDatabaseUrl(string $databaseUrl): string
    {
        return $this->databaseUrl = $databaseUrl;
    }

    public function getShopBaseUrl(): string
    {
        return $this->shopBaseUrl;
    }

    public function setShopBaseUrl(string $shopBaseUrl): void
    {
        $this->shopBaseUrl = $shopBaseUrl;
    }

    public function getEditionSourcePath(Edition $edition): string
    {
        $basicContext = BootstrapContainerFactory::getBootstrapContainer()->get(BasicContextInterface::class);
        return match ($edition) {
            Edition::Community => $this->ceSourcePath ?? $basicContext->getEditionSourcePath(Edition::Community),
            Edition::Professional => $this->peSourcePath ?? $basicContext->getEditionSourcePath(Edition::Professional),
            Edition::Enterprise => $this->eeSourcePath ?? $basicContext->getEditionSourcePath(Edition::Enterprise),
        };
    }

    public function setCommunityEditionSourcePath(string $path): void
    {
        $this->ceSourcePath = $path;
    }

    public function setProfessionalEditionSourcePath(string $path): void
    {
        $this->peSourcePath = $path;
    }

    public function setEnterpriseEditionSourcePath(string $path): void
    {
        $this->eeSourcePath = $path;
    }
}
