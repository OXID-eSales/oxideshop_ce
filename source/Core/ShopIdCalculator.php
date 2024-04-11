<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use Doctrine\DBAL\Driver\Connection;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;

/**
 * Calculates Shop id from request data or shop url.
 *
 * @internal Do not make a module extension for this class.
 */
class ShopIdCalculator
{
    /** Shop id which is used for CE/PE eShops. */
    const BASE_SHOP_ID = 1;

    /** @var array */
    private static $urlMap;

    public function __construct(
        private readonly \OxidEsales\Eshop\Core\FileCache $variablesCache,
        private ?Connection $connection = null,
    ) {
        if (!$this->connection) {
            $this->connection = ContainerFacade::get(\Doctrine\DBAL\Driver\Connection::class);
        }
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
        $urlMap = $this->getVariablesCache()->getFromCache("urlMap");
        if (!is_null($urlMap)) {
            self::$urlMap = $urlMap;

            return $urlMap;
        }

        $urlMap = [];
        foreach ($this->fetchUrlsFromConfigTable() as $row) {
            $shopId = (int)$row['oxshopid'];
            $variableName = $row['oxvarname'];
            $urlValues = $row['oxvarvalue'];

            if ($variableName === 'aLanguageURLs') {
                $urls = \unserialize($urlValues, ['allowed_classes' => false]);
                if (is_array($urls) && count($urls)) {
                    $urls = \array_filter($urls);
                    $urls = \array_fill_keys($urls, $shopId);
                    $urlMap = \array_merge($urlMap, $urls);
                }
            } elseif ($urlValues) {
                $urlMap[$urlValues] = $shopId;
            }
        }
        //save to cache
        $this->getVariablesCache()->setToCache("urlMap", $urlMap);
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
        $this->connection->connect();
        $statement = $this->connection
            ->prepare(
                "SELECT oxshopid, oxvarname, oxvarvalue
                FROM oxconfig
                WHERE oxvarname IN ('aLanguageURLs','sMallShopURL','sMallSSLShopURL')"
            );
        $statement->execute();

        return $statement->fetchAllAssociative();
    }
}
