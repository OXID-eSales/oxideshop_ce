<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;

/**
 * Seo encoder base
 *
 * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
 */
class SeoEncoderRecomm extends \OxidEsales\Eshop\Core\SeoEncoder
{
    /**
     * Returns SEO uri for tag.
     *
     * @param \OxidEsales\Eshop\Application\Model\RecommendationList $oRecomm recommendation list object
     * @param int                                                    $iLang   language
     *
     * @return string
     */
    public function getRecommUri($oRecomm, $iLang = null)
    {
        if (!($sSeoUrl = $this->_loadFromDb('dynamic', $oRecomm->getId(), $iLang))) {
            $myConfig = $this->getConfig();

            // fetching part of base url
            $sSeoUrl = $this->_getStaticUri(
                $oRecomm->getBaseStdLink($iLang, false),
                $myConfig->getShopId(),
                $iLang
            )
            . $this->_prepareTitle($oRecomm->oxrecommlists__oxtitle->value, false, $iLang);

            // creating unique
            $sSeoUrl = $this->_processSeoUrl($sSeoUrl, $oRecomm->getId(), $iLang);

            // inserting
            $this->_saveToDb('dynamic', $oRecomm->getId(), $oRecomm->getBaseStdLink($iLang), $sSeoUrl, $iLang, $myConfig->getShopId());
        }

        return $sSeoUrl;
    }

    /**
     * Returns full url for passed tag
     *
     * @param \OxidEsales\Eshop\Application\Model\RecommendationList $oRecomm recommendation list object
     * @param int                                                    $iLang   language
     *
     * @return string
     */
    public function getRecommUrl($oRecomm, $iLang = null)
    {
        if (!isset($iLang)) {
            $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        }

        return $this->_getFullUrl($this->getRecommUri($oRecomm, $iLang), $iLang);
    }

    /**
     * Returns tag SEO url for specified page
     *
     * @param \OxidEsales\Eshop\Application\Model\RecommendationList $oRecomm recommendation list object
     * @param int                                                    $iPage   page tu prepare number
     * @param int                                                    $iLang   language
     * @param bool                                                   $blFixed fixed url marker (default is false)
     *
     * @return string
     */
    public function getRecommPageUrl($oRecomm, $iPage, $iLang = null, $blFixed = false)
    {
        if (!isset($iLang)) {
            $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        }
        $sStdUrl = $oRecomm->getBaseStdLink($iLang) . '&amp;pgNr=' . $iPage;
        $sParams = (int) ($iPage + 1);

        $sStdUrl = $this->_trimUrl($sStdUrl, $iLang);
        $sSeoUrl = $this->getRecommUri($oRecomm, $iLang) . $sParams . "/";

        return $this->_getFullUrl($this->_getPageUri($oRecomm, 'dynamic', $sStdUrl, $sSeoUrl, $sParams, $iLang, $blFixed), $iLang);
    }
}
