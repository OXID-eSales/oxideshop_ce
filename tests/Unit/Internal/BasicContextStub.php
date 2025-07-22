<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\Edition;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

class BasicContextStub implements BasicContextInterface
{
    private string $communityEditionSourcePath;
    private string $edition;
    private string $enterpriseEditionRootPath;
    private string $generatedServicesFilePath;
    private string $sourcePath;
    private string $projectConfigurationDirectory;
    private string $templateCacheDirectory;
    private string $activeModuleServicesFilePath;
    private int $currentShopId;

    private BasicContextInterface $basicContext;

    public function __construct()
    {
        $this->basicContext = new BasicContext();
    }

    public function getCommunityEditionSourcePath(): string
    {
        return $this->communityEditionSourcePath ?? $this->basicContext->getCommunityEditionSourcePath();
    }

    public function setCommunityEditionSourcePath(string $communityEditionSourcePath): void
    {
        $this->communityEditionSourcePath = $communityEditionSourcePath;
    }

    public function getContainerCacheFilePath(int $shopId): string
    {
        return $this->basicContext->getContainerCacheFilePath($shopId);
    }

    public function getEdition(): Edition
    {
        return $this->edition ?? $this->basicContext->getEdition();
    }

    public function setEdition(Edition $edition): void
    {
        $this->edition = $edition;
    }

    public function getEnterpriseEditionRootPath(): string
    {
        return $this->enterpriseEditionRootPath ?? $this->basicContext->getEnterpriseEditionRootPath();
    }

    public function setEnterpriseEditionRootPath(string $enterpriseEditionRootPath): void
    {
        $this->enterpriseEditionRootPath = $enterpriseEditionRootPath;
    }

    public function getGeneratedServicesFilePath(): string
    {
        return $this->generatedServicesFilePath ?? $this->basicContext->getGeneratedServicesFilePath();
    }

    public function setGeneratedServicesFilePath(string $generatedServicesFilePath): void
    {
        $this->generatedServicesFilePath = $generatedServicesFilePath;
    }

    public function getConfigurableServicesFilePath(): string
    {
        return $this->configurableServicesFilePath ?? $this->basicContext->getConfigurableServicesFilePath();
    }

    public function setConfigurableServicesFilePath(string $configurableServicesFilePath): void
    {
        $this->configurableServicesFilePath = $configurableServicesFilePath;
    }

    public function getShopConfigurableServicesFilePath(int $shopId): string
    {
        return $this->shopConfigurableServicesFilePath ?? $this->basicContext->getShopConfigurableServicesFilePath(
            $shopId
        );
    }

    public function setShopConfigurableServicesFilePath(string $shopConfigurableServicesFilePath): void
    {
        $this->shopConfigurableServicesFilePath = $shopConfigurableServicesFilePath;
    }

    public function getProfessionalEditionRootPath(): string
    {
        return $this->professionalEditionRootPath ?? $this->basicContext->getProfessionalEditionRootPath();
    }

    public function setProfessionalEditionRootPath(string $professionalEditionRootPath): void
    {
        $this->professionalEditionRootPath = $professionalEditionRootPath;
    }

    public function getSourcePath(): string
    {
        return $this->sourcePath ?? $this->basicContext->getSourcePath();
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
        return $this->basicContext->getBackwardsCompatibilityClassMap();
    }

    public function getProjectConfigurationDirectory(): string
    {
        return $this->projectConfigurationDirectory ?? $this->basicContext->getProjectConfigurationDirectory();
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
        return $this->basicContext->getShopRootPath();
    }

    public function getOutPath(): string
    {
        return $this->basicContext->getOutPath();
    }

    public function getVendorPath(): string
    {
        return $this->basicContext->getVendorPath();
    }

    public function setVendorPath(string $path): void
    {
        $this->vendorPath = $path;
    }

    public function getComposerVendorName(): string
    {
        return $this->basicContext->getComposerVendorName();
    }

    public function getCacheDirectory(): string
    {
        return $this->basicContext->getCacheDirectory();
    }

    public function setCacheDirectory(string $cacheDirectory): void
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    public function getModuleCacheDirectory(): string
    {
        return $this->basicContext->getModuleCacheDirectory();
    }

    public function getShopConfigurationDirectory(int $shopId): string
    {
        return Path::join($this->getProjectConfigurationDirectory(), 'shops', (string)$shopId);
    }

    public function getActiveModuleServicesFilePath(int $shopId): string
    {
        return $this->activeModuleServicesFilePath ?? $this->basicContext->getActiveModuleServicesFilePath($shopId);
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