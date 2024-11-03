<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

class ShopIdCalculator
{
    public const BASE_SHOP_ID = 1;
    private static array $urlMap;

    public function __construct(
        private readonly FileCache $variablesCache,
        private readonly UtilsServer $utilsServer
    ) {
    }

    public function getShopId(): int
    {
        return self::BASE_SHOP_ID;
    }

    protected function getShopUrlMap(): array
    {
        if (!empty(self::$urlMap)) {
            return self::$urlMap;
        }

        $map = $this->variablesCache->getFromCache('urlMap');
        if ($map !== null) {
            self::$urlMap = $map;
            return $map;
        }

        $map = [];
        $select = "
            SELECT oxshopid, oxvarname, oxvarvalue
            FROM oxconfig
            WHERE oxvarname IN ('aLanguageURLs','aLanguageSSLURLs','sMallShopURL','sMallSSLShopURL')
        ";

        $result = DatabaseProvider::getMaster()->select($select);
        if ($result?->count() > 0) {
            while (!$result->EOF) {
                $shopId = (int)$result->fields[0];
                $varName = $result->fields[1];
                $url = $result->fields[2];

                if ($varName === 'aLanguageURLs' || $varName === 'aLanguageSSLURLs') {
                    $urls = unserialize($url, ['allowed_classes' => false]);
                    if (is_array($urls) && !empty($urls)) {
                        $urls = array_filter($urls);
                        $urls = array_fill_keys($urls, $shopId);
                        $map = array_merge($map, $urls);
                    }
                } elseif ($url) {
                    $map[$url] = $shopId;
                }

                $result->fetchRow();
            }
        }

        $this->variablesCache->setToCache('urlMap', $map);
        self::$urlMap = $map;

        return $map;
    }

    protected function getUtilsServer(): UtilsServer
    {
        return $this->utilsServer;
    }
}
