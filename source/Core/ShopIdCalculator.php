<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\Eshop\Core;

use oxDb;

/**
 * Calculates Shop id from request data or shop url.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ShopIdCalculator
{
    /** Shop id which is used for CE/PE eShops. */
    const BASE_SHOP_ID = 1;

    /** @var array */
    private static $urlMap;

    /** @var FileCache */
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
     * Returns configuration key. This method is independent from oxConfig functionality.
     *
     * @return string
     */
    protected function _getConfKey()
    {
        if (Registry::instanceExists('oxConfigFile')) {
            $config = Registry::get('oxConfigFile');
        } else {
            $config = new ConfigFile(getShopBasePath() . '/config.inc.php');
            Registry::set('oxConfigFile', $config);
        }
        return $config->getVar('sConfigKey') ?: Config::DEFAULT_CONFIG_KEY;
    }

    /**
     * Returns shop url to id map from config.
     *
     * @return array
     */
    protected function _getShopUrlMap()
    {
        //get from static cache
        if (isset(self::$urlMap)) {
            return self::$urlMap;
        }

        //get from file cache
        $aMap = $this->getVariablesCache()->getFromCache("urlMap");
        if (!is_null($aMap)) {
            self::$urlMap = $aMap;

            return $aMap;
        }

        $aMap = array();

        $oDb = oxDb::getDb();
        $sConfKey = $this->_getConfKey();

        $sSelect = "SELECT oxshopid, oxvarname, DECODE( oxvarvalue , " . $oDb->quote($sConfKey) . " ) as oxvarvalue " .
            "FROM oxconfig WHERE oxvarname in ('aLanguageURLs','sMallShopURL','sMallSSLShopURL')";

        //must read from master, see ESDEV-3804 for details
        $oRs = $oDb->select($sSelect, false, false);

        if ($oRs && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $iShp = (int) $oRs->fields[0];
                $sVar = $oRs->fields[1];
                $sURL = $oRs->fields[2];

                if ($sVar == 'aLanguageURLs') {
                    $aUrls = unserialize($sURL);
                    if (is_array($aUrls) && count($aUrls)) {
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
        $this->getVariablesCache()->setToCache("urlMap", $aMap);
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
