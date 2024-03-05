<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use Doctrine\DBAL\DriverManager;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;

use function array_fill_keys;
use function array_filter;
use function array_merge;
use function is_array;
use function unserialize;

/**
 * Calculates Shop id from request data or shop url.
 *
 * @internal Do not make a module extension for this class.
 */
class ShopIdCalculator
{
    /** Shop id which is used for CE/PE eShops. */
    public const BASE_SHOP_ID = 1;

    private static array $urlMap;

    public function __construct(
        private readonly \OxidEsales\Eshop\Core\FileCache $variablesCache
    ) {
    }

    /**
     * Returns active shop id. This method works independently from other classes.
     *
     * @return string
     */
    public function getShopId()
    {
        return static::BASE_SHOP_ID;
    }

    /**
     * Returns shop url to id map from config.
     *
     * @return array
     */
    protected function getShopUrlMap()
    {
        //get from static cache
        if (isset(self::$urlMap)) {
            return self::$urlMap;
        }

        //get from file cache
        $urlMap = $this->getVariablesCache()->getFromCache('urlMap');
        if ($urlMap !== null) {
            self::$urlMap = $urlMap;

            return $urlMap;
        }

        $urlMap = [];
        foreach ($this->fetchUrlsFromConfigTable() as $row) {
            $shopId = (int)$row['oxshopid'];
            $variableName = $row['oxvarname'];
            $urlValues = $row['oxvarvalue'];

            if ($variableName === 'aLanguageURLs') {
                $urls = unserialize($urlValues, ['allowed_classes' => false]);
                if (is_array($urls) && count($urls)) {
                    $urls = array_filter($urls);
                    $urls = array_fill_keys($urls, $shopId);
                    $urlMap = array_merge($urlMap, $urls);
                }
            } elseif ($urlValues) {
                $urlMap[$urlValues] = $shopId;
            }
        }
        //save to cache
        $this->getVariablesCache()->setToCache('urlMap', $urlMap);
        self::$urlMap = $urlMap;

        return $urlMap;
    }

    /**
     * @return FileCache
     */
    protected function getVariablesCache()
    {
        return $this->variablesCache;
    }

    private function fetchUrlsFromConfigTable(): array
    {
        $connection = DriverManager::getConnection(['url' => (new BasicContext())->getDatabaseUrl()]);
        $statement = $connection
            ->prepare(
                "SELECT oxshopid, oxvarname, oxvarvalue
                FROM oxconfig
                WHERE oxvarname IN ('aLanguageURLs','sMallShopURL','sMallSSLShopURL')"
            );
        $statement->execute();

        return $statement->fetchAllAssociative();
    }
}
