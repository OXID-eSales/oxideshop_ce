<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * Seo encoder base
 */
class SeoEncoderManufacturer extends \OxidEsales\Eshop\Core\SeoEncoder
{
    /**
     * Root manufacturer uri cache
     *
     * @var array
     */
    protected $_aRootManufacturerUri = null;

    /**
     * Returns target "extension" (/)
     *
     * @return string
     */
    protected function _getUrlExtension()
    {
        return '/';
    }

    /**
     * Returns part of SEO url excluding path
     *
     * @param \OxidEsales\Eshop\Application\Model\Manufacturer $oManufacturer manufacturer object
     * @param int                                              $iLang         language
     * @param bool                                             $blRegenerate  if TRUE forces seo url regeneration
     *
     * @return string
     */
    public function getManufacturerUri($oManufacturer, $iLang = null, $blRegenerate = false)
    {
        if (!isset($iLang)) {
            $iLang = $oManufacturer->getLanguage();
        }
        // load from db
        if ($blRegenerate || !($sSeoUrl = $this->_loadFromDb('oxmanufacturer', $oManufacturer->getId(), $iLang))) {
            if ($iLang != $oManufacturer->getLanguage()) {
                $sId = $oManufacturer->getId();
                $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
                $oManufacturer->loadInLang($iLang, $sId);
            }

            $sSeoUrl = '';
            if ($oManufacturer->getId() != 'root') {
                if (!isset($this->_aRootManufacturerUri[$iLang])) {
                    $oRootManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
                    $oRootManufacturer->loadInLang($iLang, 'root');
                    $this->_aRootManufacturerUri[$iLang] = $this->getManufacturerUri($oRootManufacturer, $iLang);
                }
                $sSeoUrl .= $this->_aRootManufacturerUri[$iLang];
            }

            $sSeoUrl .= $this->_prepareTitle($oManufacturer->oxmanufacturers__oxtitle->value, false, $oManufacturer->getLanguage()) . '/';
            $sSeoUrl = $this->_processSeoUrl($sSeoUrl, $oManufacturer->getId(), $iLang);

            // save to db
            $this->_saveToDb('oxmanufacturer', $oManufacturer->getId(), $oManufacturer->getBaseStdLink($iLang), $sSeoUrl, $iLang);
        }

        return $sSeoUrl;
    }

    /**
     * Returns Manufacturer SEO url for specified page
     *
     * @param \OxidEsales\Eshop\Application\Model\Manufacturer $oManufacturer manufacturer object
     * @param int                                              $iPage         page tu prepare number
     * @param int                                              $iLang         language
     * @param bool                                             $blFixed       fixed url marker (default is null)
     *
     * @return string
     */
    public function getManufacturerPageUrl($oManufacturer, $iPage, $iLang = null, $blFixed = null)
    {
        if (!isset($iLang)) {
            $iLang = $oManufacturer->getLanguage();
        }
        $sStdUrl = $oManufacturer->getBaseStdLink($iLang) . '&amp;pgNr=' . $iPage;
        $sParams = $sParams = (int) ($iPage + 1);

        $sStdUrl = $this->_trimUrl($sStdUrl, $iLang);
        $sSeoUrl = $this->getManufacturerUri($oManufacturer, $iLang) . $sParams . "/";

        if ($blFixed === null) {
            $blFixed = $this->_isFixed('oxmanufacturers', $oManufacturer->getId(), $iLang);
        }

        return $this->_getFullUrl($this->_getPageUri($oManufacturer, 'oxmanufacturers', $sStdUrl, $sSeoUrl, $sParams, $iLang, $blFixed), $iLang);
    }

    /**
     * Encodes manufacturer category URLs into SEO format
     *
     * @param \OxidEsales\Eshop\Application\Model\Manufacturer $oManufacturer Manufacturer object
     * @param int                                              $iLang         language
     *
     * @return string
     */
    public function getManufacturerUrl($oManufacturer, $iLang = null)
    {
        if (!isset($iLang)) {
            $iLang = $oManufacturer->getLanguage();
        }

        return $this->_getFullUrl($this->getManufacturerUri($oManufacturer, $iLang), $iLang);
    }

    /**
     * Deletes manufacturer seo entry
     *
     * @param \OxidEsales\Eshop\Application\Model\Manufacturer $oManufacturer Manufacturer object
     */
    public function onDeleteManufacturer($oManufacturer)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sIdQuoted = $oDb->quote($oManufacturer->getId());
        $oDb->execute("delete from oxseo where oxobjectid = $sIdQuoted and oxtype = 'oxmanufacturer'");
        $oDb->execute("delete from oxobject2seodata where oxobjectid = $sIdQuoted");
        $oDb->execute("delete from oxseohistory where oxobjectid = $sIdQuoted");
    }

    /**
     * Returns alternative uri used while updating seo
     *
     * @param string $sObjectId object id
     * @param int    $iLang     language id
     *
     * @return string
     */
    protected function _getAltUri($sObjectId, $iLang)
    {
        $sSeoUrl = null;
        $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
        if ($oManufacturer->loadInLang($iLang, $sObjectId)) {
            $sSeoUrl = $this->getManufacturerUri($oManufacturer, $iLang, true);
        }

        return $sSeoUrl;
    }
}
