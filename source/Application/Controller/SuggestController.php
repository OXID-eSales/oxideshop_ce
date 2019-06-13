<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\MailValidator;
use OxidEsales\Eshop\Core\Registry;

/**
 * Article suggestion page.
 * Collects some article base information, sets default recommendation text,
 * sends suggestion mail to user.
 *
 * @deprecated since v6.2.0 (2017-02-15); Recommendations feature will be moved to an own module.
 */
class SuggestController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/info/suggest.tpl';

    /**
     * Required fields to fill before sending suggest email
     *
     * @var array
     */
    protected $_aReqFields = ['rec_name', 'rec_email', 'send_name', 'send_email', 'send_message', 'send_subject'];

    /**
     * CrossSelling articlelist
     *
     * @var object
     */
    protected $_oCrossSelling = null;

    /**
     * Similar products articlelist
     *
     * @var object
     */
    protected $_oSimilarProducts = null;

    /**
     * Recommlist
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var object
     */
    protected $_oRecommList = null;

    /**
     * Recommlist
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var object
     */
    protected $_aSuggestData = null;

    /**
     * Assures, that controller would not be accessed if functionality disabled.
     */
    public function init()
    {
        $this->redirectToHomeIfDisabled();
        parent::init();
    }

    /**
     * Sends product suggestion mail and returns a URL according to
     * URL formatting rules.
     *
     * Template variables:
     * <b>editval</b>, <b>error</b>
     *
     * @return  null
     */
    public function send()
    {
        $aParams = Registry::getConfig()->getRequestParameter('editval', true);
        if (!is_array($aParams)) {
            return;
        }

        // storing used written values
        $oParams = (object) $aParams;
        $this->setSuggestData((object) Registry::getConfig()->getRequestParameter('editval'));

        $oUtilsView = Registry::getUtilsView();

        // filled not all fields ?
        foreach ($this->_aReqFields as $sFieldName) {
            if (!isset($aParams[$sFieldName]) || !$aParams[$sFieldName]) {
                $oUtilsView->addErrorToDisplay('SUGGEST_COMLETECORRECTLYFIELDS');

                return;
            }
        }

        if (!oxNew(MailValidator::class)->isValidEmail($aParams["rec_email"])
            || !oxNew(MailValidator::class)->isValidEmail($aParams["send_email"])
        ) {
            $oUtilsView->addErrorToDisplay('SUGGEST_INVALIDMAIL');

            return;
        }

        $sReturn = "";
        // #1834M - specialchar search
        $sSearchParamForLink = rawurlencode(Registry::getConfig()->getRequestParameter('searchparam', true));
        if ($sSearchParamForLink) {
            $sReturn .= "&searchparam=$sSearchParamForLink";
        }

        $sSearchCatId = Registry::getConfig()->getRequestParameter('searchcnid');
        if ($sSearchCatId) {
            $sReturn .= "&searchcnid=$sSearchCatId";
        }

        $sSearchVendor = Registry::getConfig()->getRequestParameter('searchvendor');
        if ($sSearchVendor) {
            $sReturn .= "&searchvendor=$sSearchVendor";
        }

        if (($sSearchManufacturer = Registry::getConfig()->getRequestParameter('searchmanufacturer'))) {
            $sReturn .= "&searchmanufacturer=$sSearchManufacturer";
        }

        $sListType = Registry::getConfig()->getRequestParameter('listtype');
        if ($sListType) {
            $sReturn .= "&listtype=$sListType";
        }

        // sending suggest email
        $oEmail = oxNew(\OxidEsales\Eshop\Core\Email::class);
        $oProduct = $this->getProduct();
        if ($oProduct && $oEmail->sendSuggestMail($oParams, $oProduct)) {
            return 'details?anid=' . $oProduct->getId() . $sReturn;
        } else {
            $oUtilsView->addErrorToDisplay('SUGGEST_INVALIDMAIL');
        }
    }

    /**
     * Template variable getter. Returns search product
     *
     * @return object
     */
    public function getProduct()
    {
        if ($this->_oProduct === null) {
            $this->_oProduct = false;

            if ($sProductId = $this->getConfig()->getRequestParameter('anid')) {
                $oProduct = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
                $oProduct->load($sProductId);
                $this->_oProduct = $oProduct;
            }
        }

        return $this->_oProduct;
    }

    /**
     * Template variable getter. Returns recommlist's reviews
     *
     * @return array
     */
    public function getCrossSelling()
    {
        if ($this->_oCrossSelling === null) {
            $this->_oCrossSelling = false;
            if ($oProduct = $this->getProduct()) {
                $this->_oCrossSelling = $oProduct->getCrossSelling();
            }
        }

        return $this->_oCrossSelling;
    }

    /**
     * Template variable getter. Returns recommlist's reviews
     *
     * @return array
     */
    public function getSimilarProducts()
    {
        if ($this->_oSimilarProducts === null) {
            $this->_oSimilarProducts = false;
            if ($oProduct = $this->getProduct()) {
                $this->_oSimilarProducts = $oProduct->getSimilarProducts();
            }
        }

        return $this->_oSimilarProducts;
    }

    /**
     * Template variable getter. Returns recommlist's reviews
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return array
     */
    public function getRecommList()
    {
        if (!$this->getViewConfig()->getShowListmania()) {
            return false;
        }

        if ($this->_oRecommList === null) {
            $this->_oRecommList = false;
            if ($oProduct = $this->getProduct()) {
                $oRecommList = oxNew(\OxidEsales\Eshop\Application\Model\RecommendationList::class);
                $this->_oRecommList = $oRecommList->getRecommListsByIds([$oProduct->getId()]);
            }
        }

        return $this->_oRecommList;
    }

    /**
     * Suggest data setter
     *
     * @param object $oData suggest data object
     */
    public function setSuggestData($oData)
    {
        $this->_aSuggestData = $oData;
    }

    /**
     * Template variable getter. Returns active object's reviews
     *
     * @return array
     */
    public function getSuggestData()
    {
        return $this->_aSuggestData;
    }

    /**
     * get link of current view
     *
     * @param int $iLang requested language
     *
     * @return string
     */
    public function getLink($iLang = null)
    {
        $sLink = parent::getLink($iLang);

        // active category
        if ($sVal = Registry::getConfig()->getRequestParameter('cnid')) {
            $sLink .= ((strpos($sLink, '?') === false) ? '?' : '&amp;') . "cnid={$sVal}";
        }

        // active article
        if ($sVal = Registry::getConfig()->getRequestParameter('anid')) {
            $sLink .= ((strpos($sLink, '?') === false) ? '?' : '&amp;') . "anid={$sVal}";
        }

        return $sLink;
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = [];
        $aPath = [];
        $iBaseLanguage = Registry::getLang()->getBaseLanguage();
        $aPath['title'] = Registry::getLang()->translateString('RECOMMEND_PRODUCT', $iBaseLanguage, false);
        $aPath['link'] = $this->getLink();

        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * In case functionality disabled, redirects to home page.
     */
    private function redirectToHomeIfDisabled()
    {
        if ($this->getConfig()->getConfigParam('blAllowSuggestArticle') !== true) {
            Registry::getUtils()->redirect($this->getConfig()->getShopHomeUrl(), true, 301);
        }
    }
}
