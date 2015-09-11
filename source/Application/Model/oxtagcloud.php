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

if (!defined('OXTAGCLOUD_MINFONT')) {
    define('OXTAGCLOUD_MINFONT', 100);
    define('OXTAGCLOUD_MAXFONT', 400);
    define('OXTAGCLOUD_MINOCCURENCETOSHOW', 2);
    define('OXTAGCLOUD_STARTPAGECOUNT', 20);
    define('OXTAGCLOUD_EXTENDEDCOUNT', 200);
}

/**
 * Class dedicated to tag cloud handling
 *
 */
class oxTagCloud extends oxSuperCfg
{

    /**
     * Cloud cache key
     *
     * @var string
     */
    protected $_sCacheKey = "tagcloud";

    /**
     * Max hit
     *
     * @var int
     */
    protected $_iMaxHit = null;

    /**
     * Cloud array
     *
     * @var array
     */
    protected $_aCloudArray = null;

    /**
     * Extended mode
     *
     * @var bool
     */
    protected $_blExtended = false;


    /**
     * Maximum tag's length
     * Maximum size of one tag in admin area and limits tag input field in front end
     *
     * @var int
     */
    protected $_iTagMaxLength = 60;

    /**
     * Object constructor. Initializes separator.
     */
    public function __construct()
    {
    }

    /**
     * Returns current maximum tag length
     *
     * @return int
     */
    public function getTagMaxLength()
    {
        return $this->_iTagMaxLength;
    }

    /**
     * Tag cloud mode setter (extended or not)
     *
     * @param bool $blExtended if true - extended cloud array will be returned
     */
    public function setExtendedMode($blExtended)
    {
        $this->_blExtended = $blExtended;
    }

    /**
     * Extended mode getter
     *
     * @return bool
     */
    public function isExtended()
    {
        return $this->_blExtended;
    }

    /**
     * Sets oxITagList object
     *
     * @param oxITagList $oTagList Tag cloud set object, which implements oxITagList
     */
    public function setTagList(oxITagList $oTagList)
    {
        $this->_oTagList = $oTagList;
    }

    /**
     * Returns oxITagList object
     *
     * @return oxITagList
     */
    public function getTagList()
    {
        return $this->_oTagList;
    }

    /**
     * Sets tag cloud array
     *
     * @param array $aTagCloudArray tag cloud array
     */
    public function setCloudArray($aTagCloudArray)
    {
        $sCacheIdent = $this->_formCacheKey();
        $this->_aCloudArray[$sCacheIdent] = $aTagCloudArray;
    }

    /**
     * Returns extended tag cloud array
     *
     * @return array
     */
    public function getCloudArray()
    {
        $sCacheIdent = $this->_formCacheKey();
        if (!isset($this->_aCloudArray[$sCacheIdent])) {
            $oTagList = $this->getTagList();

            $this->_aCloudArray[$sCacheIdent] = $this->formCloudArray($oTagList);
        }

        return $this->_aCloudArray[$sCacheIdent];
    }

    /**
     * Returns tag cloud array
     *
     * @param oxITagList $oTagList Tag List
     *
     * @return array
     */
    public function formCloudArray(oxITagList $oTagList)
    {
        // checking if current data is already loaded
        if ($oTagList->getCacheId()) {
            $sCacheIdent = $this->_formCacheKey($oTagList->getCacheId());
            $myUtils = oxRegistry::getUtils();
            // checking cache
            $aCloudArray = $myUtils->fromFileCache($sCacheIdent);
        }

        // loading cloud info
        if ($aCloudArray === null) {
            $oTagList->loadList();
            $oTagSet = $oTagList->get();
            if (count($oTagSet->get()) > $this->getMaxAmount()) {
                $oTagSet->sortByHitCount();
                $oTagSet->slice(0, $this->getMaxAmount());
            }
            $oTagSet->sort();
            $aCloudArray = $oTagSet->get();
            // updating cache
            if ($sCacheIdent) {
                $myUtils->toFileCache($sCacheIdent, $aCloudArray);
            }
        }

        return $aCloudArray;
    }

    /**
     * Returns tag size
     *
     * @param string $sTag tag title
     *
     * @return int
     */
    public function getTagSize($sTag)
    {
        $aCloudArray = $this->getCloudArray();
        if (is_null($aCloudArray[$sTag])) {
            return 1;
        }
        $iCurrSize = $this->_getFontSize($aCloudArray[$sTag]->getHitCount(), $this->_getMaxHit());

        // calculating min size
        return floor($iCurrSize / OXTAGCLOUD_MINFONT) * OXTAGCLOUD_MINFONT;
    }

    /**
     * Returns maximum amount of tags, that should be shown in list
     *
     * @return int
     */
    public function getMaxAmount()
    {
        if ($this->isExtended()) {
            return OXTAGCLOUD_EXTENDEDCOUNT;
        } else {
            return OXTAGCLOUD_STARTPAGECOUNT;
        }
    }

    /**
     * Resets tag cache
     *
     * @param int $iLang preferred language [optional]
     */
    public function resetTagCache($iLang = null)
    {
        if ($iLang) {
            $this->setLanguageId($iLang);
        }
        $this->resetCache();
    }

    /**
     * Resets tag cache
     */
    public function resetCache()
    {
        $myUtils = oxRegistry::getUtils();

        $sCacheId = null;
        if (($oTagList = $this->getTagList()) !== null) {
            $sCacheId = $oTagList->getCacheId();
        }

        $myUtils->toFileCache($this->_formCacheKey($sCacheId), null);

        $this->_aCloudArray = null;
    }

    /**
     * Returns tag cache key name.
     *
     * @param string $sTagListCacheId Whether to display full list
     *
     * @return string formed cache key
     */
    protected function _formCacheKey($sTagListCacheId = null)
    {
        $sExtended = $this->isExtended() ? '1' : '0';

        return $this->_sCacheKey . "_" . $this->getConfig()->getShopId() . "_" . $sExtended . "_" . $sTagListCacheId;
    }

    /**
     * Returns max hit
     *
     * @return int
     */
    protected function _getMaxHit()
    {
        if ($this->_iMaxHit === null) {
            $aHits = array_map(array($this, '_getTagHitCount'), $this->getCloudArray());
            $this->_iMaxHit = max($aHits);
        }

        return $this->_iMaxHit;
    }

    /**
     * Returns tag hit count. Used for _getMaxHit array mapping
     *
     * @param oxTag $oTag tag object
     *
     * @return int
     */
    protected function _getTagHitCount($oTag)
    {
        return $oTag->getHitCount();
    }

    /**
     * Returns font size value for current occurrence depending on max occurrence.
     *
     * @param int $iHit    hit count
     * @param int $iMaxHit max hits count
     *
     * @return int
     */
    protected function _getFontSize($iHit, $iMaxHit)
    {
        //handling special case
        if ($iMaxHit <= OXTAGCLOUD_MINOCCURENCETOSHOW || !$iMaxHit) {
            return OXTAGCLOUD_MINFONT;
        }

        $iFontDiff = OXTAGCLOUD_MAXFONT - OXTAGCLOUD_MINFONT;
        $iMaxHitDiff = $iMaxHit - OXTAGCLOUD_MINOCCURENCETOSHOW;
        $iHitDiff = $iHit - OXTAGCLOUD_MINOCCURENCETOSHOW;

        if ($iHitDiff < 0) {
            $iHitDiff = 0;
        }

        $iSize = round($iHitDiff * $iFontDiff / $iMaxHitDiff) + OXTAGCLOUD_MINFONT;

        return $iSize;
    }
}
