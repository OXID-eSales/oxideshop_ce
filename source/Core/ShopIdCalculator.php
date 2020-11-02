<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Calculates Shop id from request data or shop url.
 *
 * @internal do not make a module extension for this class
 *
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ShopIdCalculator
{
    /**
     * Shop id which is used for CE/PE eShops.
     */
    public const BASE_SHOP_ID = 1;

    /**
     * @var array
     */
    private static $urlMap;

    /**
     * @var FileCache
     */
    private $variablesCache;

    /**
     * @param FileCache $variablesCache
     */
    public function __construct($variablesCache)
    {
        $this->variablesCache = $variablesCache;
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
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getShopUrlMap" in next major
     */
    protected function _getShopUrlMap() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        //get from static cache
        if (isset(self::$urlMap)) {
            return self::$urlMap;
        }

        //get from file cache
        $aMap = $this->getVariablesCache()->getFromCache('urlMap');
        if (null !== $aMap) {
            self::$urlMap = $aMap;

            return $aMap;
        }

        $aMap = [];

        $sSelect = "
            SELECT oxshopid, oxvarname, oxvarvalue
            FROM oxconfig
            WHERE oxvarname IN ('aLanguageURLs','sMallShopURL','sMallSSLShopURL')";

        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
        $oRs = $masterDb->select($sSelect);

        if ($oRs && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $iShp = (int)$oRs->fields[0];
                $sVar = $oRs->fields[1];
                $sURL = $oRs->fields[2];

                if ('aLanguageURLs' === $sVar) {
                    $aUrls = unserialize($sURL);
                    if (\is_array($aUrls) && \count($aUrls)) {
                        $aUrls = array_filter($aUrls);
                        $aUrls = array_fill_keys($aUrls, $iShp);
                        $aMap = array_merge($aMap, $aUrls);
                    }
                } elseif ($sURL) {
                    $aMap[$sURL] = $iShp;
                }

                $oRs->fetchRow();
            }
        }

        //save to cache
        $this->getVariablesCache()->setToCache('urlMap', $aMap);
        self::$urlMap = $aMap;

        return $aMap;
    }

    /**
     * @return FileCache
     */
    protected function getVariablesCache()
    {
        return $this->variablesCache;
    }
}
