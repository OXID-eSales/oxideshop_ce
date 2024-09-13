<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Parameters;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\ShopBaseUrl;
use OxidEsales\EshopCommunity\Internal\Setup\Language\DefaultLanguage;

class SetupParameters
{
    private string $cacheDir;
    private DatabaseConfiguration $dbConfig;
    private ShopBaseUrl $shopBaseUrl;
    private DefaultLanguage $language;

    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    public function setCacheDir(string $cacheDir): void
    {
        $this->cacheDir = $cacheDir;
    }

    public function getDbConfig(): DatabaseConfiguration
    {
        return $this->dbConfig;
    }

    public function setDbConfig(DatabaseConfiguration $dbConfig): void
    {
        $this->dbConfig = $dbConfig;
    }

    public function getShopBaseUrl(): ShopBaseUrl
    {
        return $this->shopBaseUrl;
    }

    public function setShopBaseUrl(ShopBaseUrl $shopBaseUrl): void
    {
        $this->shopBaseUrl = $shopBaseUrl;
    }

    public function getLanguage(): DefaultLanguage
    {
        return $this->language;
    }

    public function setLanguage(DefaultLanguage $language): void
    {
        $this->language = $language;
    }
}
