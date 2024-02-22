<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Utility;

use OxidEsales\EshopCommunity\Core\Autoload\BackwardsCompatibilityClassMapProvider;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use OxidEsales\Facts\Edition\EditionSelector;
use OxidEsales\Facts\Facts;
use Symfony\Component\Filesystem\Path;

/**
 * @inheritdoc
 */
class BasicContext implements BasicContextInterface
{
    public const COMMUNITY_EDITION = EditionSelector::COMMUNITY;
    public const PROFESSIONAL_EDITION = EditionSelector::PROFESSIONAL;
    public const ENTERPRISE_EDITION = EditionSelector::ENTERPRISE;

    /**
     * @var Facts
     */
    private $facts;

    public function getContainerCacheFilePath(int $shopId): string
    {
        return Path::join($this->getCacheDirectory(), 'container', 'container_cache_shop_' . $shopId . '.php');
    }

    public function getGeneratedServicesFilePath(): string
    {
        return Path::join($this->getShopRootPath(), 'var', 'generated', 'generated_services.yaml');
    }

    public function getConfigurableServicesFilePath(): string
    {
        return Path::join($this->getShopRootPath(), 'var', 'configuration', 'configurable_services.yaml');
    }

    public function getShopConfigurableServicesFilePath(int $shopId): string
    {
        return Path::join(
            $this->getShopConfigurationDirectory($shopId),
            'configurable_services.yaml'
        );
    }

    public function getActiveModuleServicesFilePath(int $shopId): string
    {
        return Path::join($this->getShopConfigurationDirectory($shopId), 'active_module_services.yaml');
    }

    public function getSourcePath(): string
    {
        return $this->getFacts()->getSourcePath();
    }

    public function getEdition(): string
    {
        return $this->getFacts()->getEdition();
    }

    public function getCommunityEditionSourcePath(): string
    {
        return $this->getFacts()->getCommunityEditionSourcePath();
    }

    public function getProfessionalEditionRootPath(): string
    {
        return $this->getFacts()->getProfessionalEditionRootPath();
    }

    public function getOutPath(): string
    {
        return $this->getFacts()->getOutPath();
    }

    public function getEnterpriseEditionRootPath(): string
    {
        return $this->getFacts()->getEnterpriseEditionRootPath();
    }

    public function getDefaultShopId(): int
    {
        return ShopIdCalculator::BASE_SHOP_ID;
    }

    public function getCurrentShopId(): int
    {
        return $this->getDefaultShopId();
    }

    public function getAllShopIds(): array
    {
        return [
            $this->getDefaultShopId(),
        ];
    }

    public function getBackwardsCompatibilityClassMap(): array
    {
        return (new BackwardsCompatibilityClassMapProvider())->getMap();
    }

    public function getProjectConfigurationDirectory(): string
    {
        return $this->getConfigurationDirectoryPath();
    }

    public function getConfigurationDirectoryPath(): string
    {
        return $this->getShopRootPath() . '/var/configuration/';
    }

    public function getShopConfigurationDirectory(int $shopId): string
    {
        return Path::join($this->getProjectConfigurationDirectory(), 'shops', (string)$shopId);
    }

    public function getShopRootPath(): string
    {
        return $this->getFacts()->getShopRootPath();
    }

    public function getVendorPath(): string
    {
        return $this->getFacts()->getVendorPath();
    }

    public function getComposerVendorName(): string
    {
        return $this->getFacts()::COMPOSER_VENDOR_OXID_ESALES;
    }

    public function getConfigFilePath(): string
    {
        return $this->getSourcePath() . '/config.inc.php';
    }

    public function getConfigTableName(): string
    {
        return 'oxconfig';
    }

    public function getCacheDirectory(): string
    {
        return Path::join($this->getShopRootPath(), 'var', 'cache');
    }

    public function getModuleCacheDirectory(): string
    {
        return Path::join(
            $this->getCacheDirectory(),
            'modules'
        );
    }

    public function getFacts(): Facts
    {
        if ($this->facts === null) {
            $this->facts = new Facts();
        }
        return $this->facts;
    }

    public function getTemplateCacheDirectory(): string
    {
        return Path::join(
            $this->getCacheDirectory(),
            'template_cache'
        );
    }
}
