<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Utility;

use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\Edition;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\EditionDirectoriesLocator;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\EditionPaths;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\EditionResolver;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectDirectoriesLocator;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectRootLocator;
use Symfony\Component\Filesystem\Path;

use function sprintf;

class BasicContext implements BasicContextInterface
{
    private string $projectRoot;
    private string $outPath;
    private Edition $edition;

    public function getContainerCacheFilePath(int $shopId): string
    {
        return Path::join(
            $this->getCacheDirectory(),
            'container',
            sprintf('container_cache_shop_%d.%s.php', $shopId, getenv('OXID_ENV'))
        );
    }

    public function getGeneratedServicesFilePath(): string
    {
        return Path::join($this->getShopRootPath(), 'var', 'generated', 'generated_services.yaml');
    }

    public function getActiveModuleServicesFilePath(int $shopId): string
    {
        return Path::join($this->getShopConfigurationDirectory($shopId), 'active_module_services.yaml');
    }

    public function getEditionSourcePath(Edition $edition): string
    {
        return (new EditionDirectoriesLocator())->getEditionSourcePath($edition);
    }

    public function getSourcePath(): string
    {
        return Path::join($this->getShopRootPath(), 'source');
    }

    public function getEdition(): Edition
    {
        if (!isset($this->edition)) {
            $this->edition = (new EditionResolver())->getEdition();
        }
        return $this->edition;
    }

    public function getOutPath(): string
    {
        if (!isset($this->outPath)) {
            $this->outPath = (new ProjectDirectoriesLocator())->getOutPath();
        }
        return $this->outPath;
    }

    public function getDefaultShopId(): int
    {
        return ShopIdCalculator::BASE_SHOP_ID;
    }

    public function getAllShopIds(): array
    {
        return [
            $this->getDefaultShopId(),
        ];
    }

    public function getProjectConfigurationDirectory(): string
    {
        return Path::join($this->getShopRootPath(), 'var', 'configuration');
    }

    public function getShopConfigurationDirectory(int $shopId): string
    {
        return Path::join(
            $this->getProjectConfigurationDirectory(),
            'shops',
            (string)$shopId
        );
    }

    public function getShopRootPath(): string
    {
        if (!isset($this->projectRoot)) {
            $this->projectRoot = (new ProjectRootLocator())->getProjectRoot();
        }
        return $this->projectRoot;
    }

    public function getVendorPath(): string
    {
        return (new ProjectDirectoriesLocator())->getVendorPath();
    }

    public function getComposerVendorName(): string
    {
        return (EditionPaths::Community)->getVendorFolderName();
    }

    public function getConfigTableName(): string
    {
        return 'oxconfig';
    }

    public function getCacheDirectory(): string
    {
        return getenv('OXID_BUILD_DIRECTORY');
    }

    public function getModuleCacheDirectory(): string
    {
        return Path::join(
            $this->getCacheDirectory(),
            'modules'
        );
    }

    public function getDatabaseUrl(): string
    {
        return getenv('OXID_DB_URL') ?: '';
    }

    public function getShopBaseUrl(): string
    {
        return getenv('OXID_SHOP_BASE_URL');
    }
}
