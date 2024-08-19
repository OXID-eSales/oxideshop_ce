<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Utility;

use Monolog\Logger;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Exception\AdminUserNotFoundException;
use PDO;
use Symfony\Component\Filesystem\Path;

class Context extends BasicContext implements ContextInterface
{
    public function __construct(private readonly int $shopId)
    {
    }

    public function getLogLevel(): string
    {
        $envValue = getenv('OXID_LOG_LEVEL');

        return !empty($envValue) ?
            $envValue :
            strtolower(Logger::getLevelName(Logger::ERROR));
    }

    public function getLogFilePath(): string
    {
        return Path::join($this->getSourcePath(), 'log', 'oxideshop.log');
    }

    public function getRequiredContactFormFields(): array
    {
        $contactFormRequiredFields = $this->getConfigParameter('contactFormRequiredFields');

        return $contactFormRequiredFields ?? [];
    }

    public function getCurrentShopId(): int
    {
        return $this->shopId;
    }

    public function getAllShopIds(): array
    {
        $integerShopIds = [];

        foreach (Registry::getConfig()->getShopIds() as $shopId) {
            $integerShopIds[] = (int)$shopId;
        }

        return $integerShopIds;
    }

    public function isAdmin(): bool
    {
        return $this->isConfigLoaded() ? Registry::getConfig()->isAdmin() : isAdmin();
    }

    public function getAdminLogFilePath(): string
    {
        return Path::join($this->getSourcePath(), 'log', 'oxadmin.log');
    }

    /**
     * We need to be careful when trying to fetch config parameters in this place as the
     * shop might still be bootstrapping.
     * The config must be already initialized before we can safely call Config::getConfigParam().
     */
    public function getSkipLogTags(): array
    {
        $skipLogTags = [];
        if ($this->isConfigLoaded()) {
            $skipLogTags = Registry::getConfig()->getConfigParam('aLogSkipTags');
        }

        return (array)$skipLogTags;
    }

    public function getAdminUserId(): string
    {
        $adminUserId = (string)Registry::getSession()->getVariable('auth');
        if (empty($adminUserId)) {
            throw new AdminUserNotFoundException();
        }

        return $adminUserId;
    }

    public function isShopInProductiveMode(): bool
    {
        return (bool)Registry::getConfig()->isProductiveMode();
    }

    public function isShopInDemoMode(): bool
    {
        return (bool)Registry::getConfig()->isDemoShop();
    }

    /**
     * @return mixed
     */
    private function getConfigParameter($name, $default = null)
    {
        $value = Registry::getConfig()->getConfigParam($name, $default);
        DatabaseProvider::getDb()->setFetchMode(PDO::FETCH_ASSOC);

        return $value;
    }

    private function isConfigLoaded(): bool
    {
        return Registry::instanceExists(Config::class);
    }
}
