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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Seo encoder base
 *
 */
class oxSeoEncoderTag extends oxSeoEncoder
{

    /**
     * Tag preparation util object
     *
     * @var oxtagcloud
     */
    protected $_oTagPrepareUtil = null;

    /**
     * Returns SEO uri for tag.
     *
     * @param string $sTag  tag
     * @param int    $iLang language
     * @param string $sOxid object id [optional]
     *
     * @return string
     */
    public function getTagUri($sTag, $iLang = null, $sOxid = null)
    {
        return $this->_getDynamicTagUri($sTag, $this->getStdTagUri($sTag), "tag/{$sTag}/", $iLang, $sOxid);
    }

    /**
     * Returns dynamic object SEO URI
     *
     * @param string $sTag    tag
     * @param string $sStdUrl standart url
     * @param string $sSeoUrl seo uri
     * @param int    $iLang   active language
     * @param string $sOxid   object id [optional]
     *
     * @return string
     */
    protected function _getDynamicTagUri($sTag, $sStdUrl, $sSeoUrl, $iLang, $sOxid = null)
    {
        $iShopId = $this->getConfig()->getShopId();

        $sStdUrl = $this->_trimUrl($sStdUrl);
        $sObjectId = $this->getDynamicObjectId($iShopId, $sStdUrl);
        $sSeoUrl = $this->_prepareUri($this->addLanguageParam($sSeoUrl, $iLang), $iLang);

        //load details link from DB
        $sOldSeoUrl = $this->_loadFromDb('dynamic', $sObjectId, $iLang);
        if ($sOldSeoUrl === $sSeoUrl) {
            $sSeoUrl = $sOldSeoUrl;
        } else {
            if ($sOldSeoUrl) {
                // old must be transferred to history
                $this->_copyToHistory($sObjectId, $iShopId, $iLang, 'dynamic');
            }
            // creating unique
            $sSeoUrl = $this->_processSeoUrl($sSeoUrl, $sObjectId, $iLang);

            // inserting
            $this->_saveToDb('dynamic', $sObjectId, $sStdUrl, $sSeoUrl, $iLang, $iShopId);
        }

        return $sSeoUrl;
    }

    /**
     * Prepares tag for search in db
     *
     * @param string $sTag tag to prepare
     *
     * @return string
     */
    protected function _prepareTag($sTag)
    {
        if ($this->_oTagPrepareUtil == null) {
            $this->_oTagPrepareUtil = oxNew('oxtagcloud');
        }

        return $sTag = $this->_oTagPrepareUtil->prepareTags($sTag);
    }

    /**
     * Returns standard tag url
     *
     * @param string $sTag           tag
     * @param bool   $blIncludeIndex if you need only parameters set this param to false (optional)
     *
     * @return string
     */
    public function getStdTagUri($sTag, $blIncludeIndex = true)
    {
        // while tags are just strings, standard ulrs formatted stays here
        $sUri = "cl=tag&amp;searchtag=" . rawurlencode($sTag);
        if ($blIncludeIndex) {
            $sUri = "index.php?" . $sUri;
        }

        return $sUri;
    }

    /**
     * Returns full url for passed tag
     *
     * @param string $sTag  tag
     * @param int    $iLang language
     *
     * @return string
     */
    public function getTagUrl($sTag, $iLang = null)
    {
        if (!isset($iLang)) {
            $iLang = oxRegistry::getLang()->getBaseLanguage();
        }

        return $this->_getFullUrl($this->getTagUri($sTag, $iLang), $iLang);
    }

    /**
     * Returns tag SEO url for specified page
     *
     * @param string $sTag    manufacturer object
     * @param int    $iPage   page tu prepare number
     * @param int    $iLang   language
     * @param bool   $blFixed fixed url marker (default is false)
     *
     * @return string
     */
    public function getTagPageUrl($sTag, $iPage, $iLang = null, $blFixed = false)
    {
        if (!isset($iLang)) {
            $iLang = oxRegistry::getLang()->getBaseLanguage();
        }
        $sStdUrl = $this->getStdTagUri($sTag) . '&amp;pgNr=' . $iPage;
        $sParams = (int) ($iPage + 1);

        $sStdUrl = $this->_trimUrl($sStdUrl, $iLang);
        $sSeoUrl = $this->getTagUri($sTag, $iLang) . $sParams . "/";

        return $this->_getFullUrl($this->_getDynamicTagUri($sTag, $sStdUrl, $sSeoUrl, $iLang), $iLang);
    }
}
