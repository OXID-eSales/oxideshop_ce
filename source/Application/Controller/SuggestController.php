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

namespace OxidEsales\EshopCommunity\Application\Controller;

use oxRegistry;

/**
 * Article suggestion page.
 * Collects some article base information, sets default recomendation text,
 * sends suggestion mail to user.
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
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editval', true);
        if (!is_array($aParams)) {
            return;
        }

        // storing used written values
        $oParams = (object) $aParams;
        $this->setSuggestData((object) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editval'));

        $oUtilsView = \OxidEsales\Eshop\Core\Registry::getUtilsView();

        // filled not all fields ?
        foreach ($this->_aReqFields as $sFieldName) {
            if (!isset($aParams[$sFieldName]) || !$aParams[$sFieldName]) {
                $oUtilsView->addErrorToDisplay('SUGGEST_COMLETECORRECTLYFIELDS');

                return;
            }
        }

        $oUtils = \OxidEsales\Eshop\Core\Registry::getUtils();
        if (!oxNew(\OxidEsales\Eshop\Core\MailValidator::class)->isValidEmail($aParams["rec_email"]) || !oxNew(\OxidEsales\Eshop\Core\MailValidator::class)->isValidEmail($aParams["send_email"])) {
            $oUtilsView->addErrorToDisplay('SUGGEST_INVALIDMAIL');

            return;
        }

        $sReturn = "";
        // #1834M - specialchar search
        $sSearchParamForLink = rawurlencode(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('searchparam', true));
        if ($sSearchParamForLink) {
            $sReturn .= "&searchparam=$sSearchParamForLink";
        }

        $sSearchCatId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('searchcnid');
        if ($sSearchCatId) {
            $sReturn .= "&searchcnid=$sSearchCatId";
        }

        $sSearchVendor = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('searchvendor');
        if ($sSearchVendor) {
            $sReturn .= "&searchvendor=$sSearchVendor";
        }

        if (($sSearchManufacturer = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('searchmanufacturer'))) {
            $sReturn .= "&searchmanufacturer=$sSearchManufacturer";
        }

        $sListType = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('listtype');
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
        if ($sVal = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('cnid')) {
            $sLink .= ((strpos($sLink, '?') === false) ? '?' : '&amp;') . "cnid={$sVal}";
        }

        // active article
        if ($sVal = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('anid')) {
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
        $iBaseLanguage = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $aPath['title'] = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('RECOMMEND_PRODUCT', $iBaseLanguage, false);
        $aPath['link'] = $this->getLink();

        $aPaths[] = $aPath;

        return $aPaths;
    }
}
