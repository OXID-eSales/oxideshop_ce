<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Utility;

use OxidEsales\EshopCommunity\Core\Autoload\BackwardsCompatibilityClassMapProvider;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use OxidEsales\Facts\Config\ConfigFile;
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

    /**
     * @return string
     */
    public function getContainerCacheFilePath(int $shopId): string
    {
        return Path::join($this->getCacheDirectory(), 'container', 'container_cache_shop_' . $shopId . '.php');
    }

    /**
     * @return string
     */
    public function getGeneratedServicesFilePath(): string
    {
        return Path::join($this->getShopRootPath(), 'var', 'generated', 'generated_services.yaml');
    }

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function getSourcePath(): string
    {
        return $this->getFacts()->getSourcePath();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getEdition(): string
    {
        return $this->getFacts()->getEdition();
    }

    /**
     * @return string
     */
    public function getCommunityEditionSourcePath(): string
    {
        return $this->getFacts()->getCommunityEditionSourcePath();
    }

    /**
     * @return string
     */
    public function getProfessionalEditionRootPath(): string
    {
        return $this->getFacts()->getProfessionalEditionRootPath();
    }

    /**
     * @return string
     */
    public function getOutPath(): string
    {
        return $this->getFacts()->getOutPath();
    }

    /**
     * @return string
     */
    public function getEnterpriseEditionRootPath(): string
    {
        return $this->getFacts()->getEnterpriseEditionRootPath();
    }

    /**
     * @return int
     */
    public function getDefaultShopId(): int
    {
        return ShopIdCalculator::BASE_SHOP_ID;
    }

    /**
     * @return int
     */
    public function getCurrentShopId(): int
    {
        return $this->getDefaultShopId();
    }

    /**
     * @return array
     */
    public function getAllShopIds(): array
    {
        return [
            $this->getDefaultShopId(),
        ];
    }

    /**
     * @return array
     */
    public function getBackwardsCompatibilityClassMap(): array
    {
        return (new BackwardsCompatibilityClassMapProvider())->getMap();
    }

    /**
     * @return string
     */
    public function getProjectConfigurationDirectory(): string
    {
        return $this->getConfigurationDirectoryPath();
    }

    /**
     * @return string
     */
    public function getConfigurationDirectoryPath(): string
    {
        return $this->getShopRootPath() . '/var/configuration/';
    }

    /**
     * @return string
     */
    public function getShopConfigurationDirectory(int $shopId): string
    {
        return Path::join($this->getProjectConfigurationDirectory(), 'shops', (string) $shopId);
    }

    /**
     * @return string
     */
    public function getShopRootPath(): string
    {
        return $this->getFacts()->getShopRootPath();
    }

    /**
     * @return string
     */
    public function getVendorPath(): string
    {
        return $this->getFacts()->getVendorPath();
    }

    /**
     * @return string
     */
    public function getComposerVendorName(): string
    {
        return $this->getFacts()::COMPOSER_VENDOR_OXID_ESALES;
    }

    /**
     * @return string
     */
    public function getConfigFilePath(): string
    {
        return $this->getSourcePath() . '/config.inc.php';
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
    public function getCacheDirectory(): string
    {
        return (new ConfigFile())->getVar('sCompileDir');
    }

    public function getModuleCacheDirectory(): string
    {
        return Path::join(
            $this->getCacheDirectory(),
            'modules'
        );
    }

    /**
     * @return Facts
     */
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
