<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Date manipulation utility class
 */
class UtilsCount extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Users view id, used to identify current state cache
     *
     * @var string
     */
    protected $_sUserViewId = null;

    /**
     * Returns category article count
     *
     * @param string $sCatId Category Id
     *
     * @return int
     */
    public function getCatArticleCount($sCatId)
    {
        // current status unique ident
        $sActIdent = $this->_getUserViewId();

        // loading from cache
        $aCatData = $this->_getCatCache();

        if (!$aCatData || !isset($aCatData[$sCatId][$sActIdent])) {
            $iCnt = $this->setCatArticleCount($aCatData, $sCatId, $sActIdent);
        } else {
            $iCnt = $aCatData[$sCatId][$sActIdent];
        }

        return $iCnt;
    }

    /**
     * Returns category article count price
     *
     * @param string $sCatId     Category Id
     * @param double $dPriceFrom from price
     * @param double $dPriceTo   to price
     *
     * @return int
     */
    public function getPriceCatArticleCount($sCatId, $dPriceFrom, $dPriceTo)
    {
        // current status unique ident
        $sActIdent = $this->_getUserViewId();

        // loading from cache
        $aCatData = $this->_getCatCache();

        if (!$aCatData || !isset($aCatData[$sCatId][$sActIdent])) {
            $iCnt = $this->setPriceCatArticleCount($aCatData, $sCatId, $sActIdent, $dPriceFrom, $dPriceTo);
        } else {
            $iCnt = $aCatData[$sCatId][$sActIdent];
        }

        return $iCnt;
    }

    /**
     * Returns vendor article count
     *
     * @param string $sVendorId Vendor category Id
     *
     * @return int
     */
    public function getVendorArticleCount($sVendorId)
    {
        // current category unique ident
        $sActIdent = $this->_getUserViewId();

        // loading from cache
        $aVendorData = $this->_getVendorCache();

        if (!$aVendorData || !isset($aVendorData[$sVendorId][$sActIdent])) {
            $iCnt = $this->setVendorArticleCount($aVendorData, $sVendorId, $sActIdent);
        } else {
            $iCnt = $aVendorData[$sVendorId][$sActIdent];
        }

        return $iCnt;
    }

    /**
     * Returns Manufacturer article count
     *
     * @param string $sManufacturerId Manufacturer category Id
     *
     * @return int
     */
    public function getManufacturerArticleCount($sManufacturerId)
    {
        // current category unique ident
        $sActIdent = $this->_getUserViewId();

        // loading from cache
        $aManufacturerData = $this->_getManufacturerCache();
        if (!$aManufacturerData || !isset($aManufacturerData[$sManufacturerId][$sActIdent])) {
            $iCnt = $this->setManufacturerArticleCount($aManufacturerData, $sManufacturerId, $sActIdent);
        } else {
            $iCnt = $aManufacturerData[$sManufacturerId][$sActIdent];
        }

        return $iCnt;
    }

    /**
     * Saves and returns category article count into cache
     *
     * @param array  $aCache    Category cache data
     * @param string $sCatId    Unique category identifier
     * @param string $sActIdent ID
     *
     * @return int
     */
    public function setCatArticleCount($aCache, $sCatId, $sActIdent)
    {
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $sTable = $oArticle->getViewName();
        $sO2CView = getViewName('oxobject2category');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        // we use distinct if article is assigned to category twice
        $sQ = "SELECT COUNT( DISTINCT $sTable.`oxid` )
               FROM $sO2CView
                   INNER JOIN $sTable ON $sO2CView.`oxobjectid` = $sTable.`oxid` AND $sTable.`oxparentid` = ''
               WHERE $sO2CView.`oxcatnid` = :oxcatnid AND " . $oArticle->getSqlActiveSnippet();

        $aCache[$sCatId][$sActIdent] = $oDb->getOne($sQ, [
            ':oxcatnid' => $sCatId
        ]);

        $this->_setCatCache($aCache);

        return $aCache[$sCatId][$sActIdent];
    }

    /**
     * Saves (if needed) and returns price category article count into cache
     *
     * @param array  $aCache     Category cache data
     * @param string $sCatId     Unique category ident
     * @param string $sActIdent  Category ID
     * @param int    $dPriceFrom Price from
     * @param int    $dPriceTo   Price to
     *
     * @return null
     */
    public function setPriceCatArticleCount($aCache, $sCatId, $sActIdent, $dPriceFrom, $dPriceTo)
    {
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $sTable = $oArticle->getViewName();

        $params = [];
        $sSelect = "SELECT count({$sTable}.oxid) FROM {$sTable} WHERE oxvarminprice >= 0";
        if ($dPriceTo) {
            $sSelect .= " AND oxvarminprice <= :oxvarpriceto";
            $params[':oxvarpriceto'] = (double) $dPriceTo;
        }

        if ($dPriceFrom) {
            $sSelect .= " AND oxvarminprice  >= :oxvarpricefrom";
            $params[':oxvarpricefrom'] = (double) $dPriceFrom;
        }

        $sSelect .=  " AND {$sTable}.oxissearch = 1 AND " . $oArticle->getSqlActiveSnippet();

        $aCache[$sCatId][$sActIdent] = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($sSelect, $params);

        $this->_setCatCache($aCache);

        return $aCache[$sCatId][$sActIdent];
    }

    /**
     * Saves and returns vendors category article count into cache
     *
     * @param array  $aCache    Category cache data
     * @param string $sCatId    Unique vendor category ident
     * @param string $sActIdent Vendor category ID
     *
     * @return int
     */
    public function setVendorArticleCount($aCache, $sCatId, $sActIdent)
    {
        // if vendor/category name is 'root', skip counting
        if ($sCatId == 'root') {
            return 0;
        }

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $sTable = $oArticle->getViewName();

        // select each vendor articles count
        $sQ = "select $sTable.oxvendorid AS vendorId, count(*) from $sTable where ";
        $sQ .= "$sTable.oxvendorid <> '' and $sTable.oxparentid = '' and " . $oArticle->getSqlActiveSnippet() . " group by $sTable.oxvendorid ";
        $aDbResult = $this->getAssoc($sQ);

        foreach ($aDbResult as $sKey => $sValue) {
            $aCache[$sKey][$sActIdent] = $sValue;
        }

        $this->_setVendorCache($aCache);

        return isset($aCache[$sCatId][$sActIdent]) ? $aCache[$sCatId][$sActIdent] : 0;
    }

    /**
     * Returns the query result as a two dimensional associative array.
     * The keys of the first level are the firsts value of each row.
     * The values of the first level arrays with numeric key that hold the all the values of each row but the first one,
     * which is used a a key in the first level.
     *
     * @param string $query
     * @param array  $parameters
     *
     * @return array
     */
    protected function getAssoc($query, $parameters = [])
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);

        $resultSet = $database->select($query, $parameters);

        $rows = $resultSet->fetchAll();

        if (!$rows) {
            return [];
        }

        $result = [];

        foreach ($rows as $row) {
            $firstColumn = array_keys($row)[0];
            $key = $row[$firstColumn];

            $values = array_values($row);

            if (2 <= count($values)) {
                $result[$key] = $values[1];
            }
        }

        return $result;
    }

    /**
     * Saves and returns Manufacturers category article count into cache
     *
     * @param array  $aCache    Category cache data
     * @param string $sMnfId    Unique Manufacturer ident
     * @param string $sActIdent Unique user context ID
     *
     * @return int
     */
    public function setManufacturerArticleCount($aCache, $sMnfId, $sActIdent)
    {
        // if Manufacturer/category name is 'root', skip counting
        if ($sMnfId == 'root') {
            return 0;
        }

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $sArtTable = $oArticle->getViewName();

        // select each Manufacturer articles count
        //#3485
        $sQ = "SELECT count($sArtTable.oxid) FROM $sArtTable WHERE $sArtTable.oxparentid = '' AND oxmanufacturerid = :manufacturerId AND " . $oArticle->getSqlActiveSnippet();
        $iValue = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($sQ, [
            ':manufacturerId' => $sMnfId
        ]);

        $aCache[$sMnfId][$sActIdent] = (int) $iValue;

        $this->_setManufacturerCache($aCache);

        return $aCache[$sMnfId][$sActIdent];
    }

    /**
     * Resets category (all categories) article count
     *
     * @param string $sCatId Category/vendor/manufacturer ID
     */
    public function resetCatArticleCount($sCatId = null)
    {
        if (!$sCatId) {
            $this->getConfig()->setGlobalParameter('aLocalCatCache', null);
            \OxidEsales\Eshop\Core\Registry::getUtils()->toFileCache('aLocalCatCache', '');
        } else {
            // loading from cache
            $aCatData = $this->_getCatCache();
            if (isset($aCatData[$sCatId])) {
                unset($aCatData[$sCatId]);
                $this->_setCatCache($aCatData);
            }
        }
    }

    /**
     * Resets price categories article count
     *
     * @param int $iPrice article price
     */
    public function resetPriceCatArticleCount($iPrice)
    {
        // loading from cache
        if ($aCatData = $this->_getCatCache()) {
            $sTable = getViewName('oxcategories');
            $sSelect = "SELECT $sTable.oxid FROM $sTable WHERE :oxpricefrom >= $sTable.oxpricefrom AND :oxpriceto <= $sTable.oxpriceto ";

            // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
            $rs = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster()->select($sSelect, [
                ':oxpricefrom' => (double) $iPrice,
                ':oxpriceto' => (double) $iPrice
            ]);
            if ($rs != false && $rs->count() > 0) {
                while (!$rs->EOF) {
                    if (isset($aCatData[$rs->fields[0]])) {
                        unset($aCatData[$rs->fields[0]]);
                    }
                    $rs->fetchRow();
                }

                // writing back to cache
                $this->_setCatCache($aCatData);
            }
        }
    }

    /**
     * Resets vendor (all vendors) article count
     *
     * @param string $sVendorId Category/vendor ID
     */
    public function resetVendorArticleCount($sVendorId = null)
    {
        if (!$sVendorId) {
            $this->getConfig()->setGlobalParameter('aLocalVendorCache', null);
            \OxidEsales\Eshop\Core\Registry::getUtils()->toFileCache('aLocalVendorCache', '');
        } else {
            // loading from cache
            $aVendorData = $this->_getVendorCache();
            if (isset($aVendorData[$sVendorId])) {
                unset($aVendorData[$sVendorId]);
                $this->_setVendorCache($aVendorData);
            }
        }
    }

    /**
     * Resets Manufacturer (all Manufacturers) article count
     *
     * @param string $sManufacturerId Category/Manufacturer ID
     */
    public function resetManufacturerArticleCount($sManufacturerId = null)
    {
        if (!$sManufacturerId) {
            $this->getConfig()->setGlobalParameter('aLocalManufacturerCache', null);
            \OxidEsales\Eshop\Core\Registry::getUtils()->toFileCache('aLocalManufacturerCache', '');
        } else {
            // loading from cache
            $aManufacturerData = $this->_getManufacturerCache();
            if (isset($aManufacturerData[$sManufacturerId])) {
                unset($aManufacturerData[$sManufacturerId]);
                $this->_setManufacturerCache($aManufacturerData);
            }
        }
    }

    /**
     * Loads and returns category cache data array
     *
     * @return array
     */
    protected function _getCatCache()
    {
        $myConfig = $this->getConfig();

        // first look at the local cache
        $aLocalCatCache = $myConfig->getGlobalParameter('aLocalCatCache');

        // if local cache is not set - loading from file cache
        if (!$aLocalCatCache) {
            $sLocalCatCache = \OxidEsales\Eshop\Core\Registry::getUtils()->fromFileCache('aLocalCatCache');
            if ($sLocalCatCache) {
                $aLocalCatCache = $sLocalCatCache;
            } else {
                $aLocalCatCache = null;
            }
            $myConfig->setGlobalParameter('aLocalCatCache', $aLocalCatCache);
        }

        return $aLocalCatCache;
    }

    /**
     * Writes category data into cache
     *
     * @param array $aCache A cacheable data
     */
    protected function _setCatCache($aCache)
    {
        $this->getConfig()->setGlobalParameter('aLocalCatCache', $aCache);
        \OxidEsales\Eshop\Core\Registry::getUtils()->toFileCache('aLocalCatCache', $aCache);
    }

    /**
     * Writes vendor data into cache
     *
     * @param array $aCache A cacheable data
     */
    protected function _setVendorCache($aCache)
    {
        $this->getConfig()->setGlobalParameter('aLocalVendorCache', $aCache);
        \OxidEsales\Eshop\Core\Registry::getUtils()->toFileCache('aLocalVendorCache', $aCache);
    }

    /**
     * Writes Manufacturer data into cache
     *
     * @param array $aCache A cacheable data
     */
    protected function _setManufacturerCache($aCache)
    {
        $this->getConfig()->setGlobalParameter('aLocalManufacturerCache', $aCache);
        \OxidEsales\Eshop\Core\Registry::getUtils()->toFileCache('aLocalManufacturerCache', $aCache);
    }

    /**
     * Loads and returns category/vendor cache data array
     *
     * @return array
     */
    protected function _getVendorCache()
    {
        $myConfig = $this->getConfig();

        // first look at the local cache
        $aLocalVendorCache = $myConfig->getGlobalParameter('aLocalVendorCache');
        // if local cache is not set - loading from file cache
        if (!$aLocalVendorCache) {
            $sLocalVendorCache = \OxidEsales\Eshop\Core\Registry::getUtils()->fromFileCache('aLocalVendorCache');
            if ($sLocalVendorCache) {
                $aLocalVendorCache = $sLocalVendorCache;
            } else {
                $aLocalVendorCache = null;
            }
            $myConfig->setGlobalParameter('aLocalVendorCache', $aLocalVendorCache);
        }

        return $aLocalVendorCache;
    }

    /**
     * Loads and returns category/Manufacturer cache data array
     *
     * @return array
     */
    protected function _getManufacturerCache()
    {
        $myConfig = $this->getConfig();

        // first look at the local cache
        $aLocalManufacturerCache = $myConfig->getGlobalParameter('aLocalManufacturerCache');
        // if local cache is not set - loading from file cache
        if (!$aLocalManufacturerCache) {
            $sLocalManufacturerCache = \OxidEsales\Eshop\Core\Registry::getUtils()->fromFileCache('aLocalManufacturerCache');
            if ($sLocalManufacturerCache) {
                $aLocalManufacturerCache = $sLocalManufacturerCache;
            } else {
                $aLocalManufacturerCache = null;
            }
            $myConfig->setGlobalParameter('aLocalManufacturerCache', $aLocalManufacturerCache);
        }

        return $aLocalManufacturerCache;
    }

    /**
     * Returns user view id (Shop, language, RR group index...)
     *
     * @param bool $blReset optional, default = false
     *
     * @return string
     */
    protected function _getUserViewId($blReset = false)
    {
        if ($this->_sUserViewId != null && !$blReset) {
            return $this->_sUserViewId;
        }

        // loading R&R data from session
        $userSessionGroups = $this->getCurrentUserSessionGroups();
        $this->_sUserViewId = md5($this->getConfig()->getShopID() . \OxidEsales\Eshop\Core\Registry::getLang()->getLanguageTag() . serialize($userSessionGroups) . (int) $this->isAdmin());

        return $this->_sUserViewId;
    }

    /**
     * Get current user groups
     *
     * @return array|null
     */
    protected function getCurrentUserSessionGroups()
    {
        return null;
    }
}
