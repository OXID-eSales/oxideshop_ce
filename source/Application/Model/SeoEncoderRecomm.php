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
     * @param \OxidEsales\Eshop\Application\Model\RecommendationList $recomm     Recommendation list object.
     * @param int                                                    $pageNumber Number of the page which should be prepared.
     * @param int                                                    $languageId Language id.
     * @param bool                                                   $isFixed    Fixed url marker (default is null).
     *
     * @return string
     */
    public function getRecommPageUrl($recomm, $pageNumber, $languageId = null, $isFixed = false)
    {
        if (!isset($languageId)) {
            $languageId = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        }
        $stdUrl = $recomm->getBaseStdLink($languageId);
        $parameters = null;

        $stdUrl = $this->_trimUrl($stdUrl, $languageId);
        $seoUrl = $this->getRecommUri($recomm, $languageId);

        return $this->assembleFullPageUrl($recomm, 'dynamic', $stdUrl, $seoUrl, $pageNumber, $parameters, $languageId, $isFixed);
    }
}
