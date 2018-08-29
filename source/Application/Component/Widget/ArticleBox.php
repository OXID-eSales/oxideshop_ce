<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component\Widget;

use oxRegistry;
use oxArticle;

/**
 * Article box widget
 */
class ArticleBox extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
{
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * User component used in template.
     *
     * @var array
     */
    protected $_aComponentNames = ['oxcmp_user' => 1, 'oxcmp_basket' => 1, 'oxcmp_cur' => 1];

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sTemplate = 'widget/product/boxproduct.tpl';

    /**
     * Current article
     *
     * @var \OxidEsales\Eshop\Application\Model\Article|null
     */
    protected $_oArticle = null;

    /**
     * Returns active category
     *
     * @return null|oxCategory
     */
    public function getActiveCategory()
    {
        $oCategory = $this->getConfig()->getTopActiveView()->getActiveCategory();
        if ($oCategory) {
            $this->setActiveCategory($oCategory);
        }

        return $this->_oActCategory;
    }

    /**
     * Renders template based on widget type or just use directly passed path of template
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $sWidgetType = $this->getViewParameter('sWidgetType');
        $sListType = $this->getViewParameter('sListType');

        if ($sWidgetType && $sListType) {
            $this->_sTemplate = "widget/" . $sWidgetType . "/" . $sListType . ".tpl";
        }

        $sForceTemplate = $this->getViewParameter('oxwtemplate');
        if ($sForceTemplate) {
            $this->_sTemplate = $sForceTemplate;
        }

        return $this->_sTemplate;
    }

    /**
     * Sets box product
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle Box product
     */
    public function setProduct($oArticle)
    {
        $this->_oArticle = $oArticle;
    }

    /**
     * Get product article
     *
     * @return \OxidEsales\Eshop\Application\Model\Article
     */
    public function getProduct()
    {
        if (is_null($this->_oArticle)) {
            if ($this->getViewParameter('_object')) {
                $oArticle = $this->getViewParameter('_object');
            } else {
                $sAddDynParams = $this->getConfig()->getTopActiveView()->getAddUrlParams();

                $sAddDynParams = $this->updateDynamicParameters($sAddDynParams);

                $oArticle = $this->_getArticleById($this->getViewParameter('anid'));
                $this->_addDynParamsToLink($sAddDynParams, $oArticle);
            }

            $this->setProduct($oArticle);
        }

        return $this->_oArticle;
    }

    /**
     * get link of current top view
     *
     * @param int $iLang requested language
     *
     * @return string
     */
    public function getLink($iLang = null)
    {
        return $this->getConfig()->getTopActiveView()->getLink($iLang);
    }

    /**
     * Returns if VAT is included in price
     *
     * @return bool
     */
    public function isVatIncluded()
    {
        return (bool) $this->getViewParameter("isVatIncluded");
    }

    /**
     * Returns wish list id
     *
     * @return string
     */
    public function getWishId()
    {
        return $this->getViewParameter('owishid');
    }

    /**
     * Returns remove function
     *
     * @return string
     */
    public function getRemoveFunction()
    {
        return $this->getViewParameter('removeFunction');
    }

    /**
     * Returns toBasket function
     *
     * @return string
     */
    public function getToBasketFunction()
    {
        return $this->getViewParameter('toBasketFunction');
    }

    /**
     * Returns if toCart must be disabled
     *
     * @return bool
     */
    public function getDisableToCart()
    {
        return (bool) $this->getViewParameter('blDisableToCart');
    }

    /**
     * Returns list item id with identifier
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->getViewParameter('iIndex');
    }

    /**
     * Returns recommendation id
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return string
     */
    public function getRecommId()
    {
        return $this->getViewParameter('recommid');
    }

    /**
     * Returns iteration number
     *
     * @return string
     */
    public function getIteration()
    {
        return $this->getViewParameter('iIteration');
    }

    /**
     * Returns RSS links
     *
     * @return array|null
     */
    public function getRSSLinks()
    {
        $aRSS = $this->getViewParameter('rsslinks');
        if (!is_array($aRSS)) {
            $aRSS = null;
        }

        return $aRSS;
    }

    /**
     * Returns the answer if main link must be showed
     *
     * @return bool
     */
    public function getShowMainLink()
    {
        return (bool) $this->getViewParameter('showMainLink');
    }

    /**
     * Returns if alternate product exists
     *
     * @return bool
     */
    public function getAltProduct()
    {
        return (bool) $this->getViewParameter('altproduct');
    }

    /**
     * Appends dyn params to url.
     *
     * @param string                                      $sAddDynParams Dyn params
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle      Article
     *
     * @return bool
     */
    protected function _addDynParamsToLink($sAddDynParams, $oArticle)
    {
        $blAddedParams = false;
        if ($sAddDynParams) {
            $blSeo = \OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive();
            if (!$blSeo) {
                // only if seo is off..
                $oArticle->appendStdLink($sAddDynParams);
            }
            $oArticle->appendLink($sAddDynParams);
            $blAddedParams = true;
        }

        return $blAddedParams;
    }

    /**
     * Returns prepared article by id.
     *
     * @param string $sArticleId Article id
     *
     * @return \OxidEsales\Eshop\Application\Model\Article
     */
    protected function _getArticleById($sArticleId)
    {
        /** @var \OxidEsales\Eshop\Application\Model\Article $oArticle */
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->load($sArticleId);
        $iLinkType = $this->getViewParameter('iLinkType');

        if ($this->getViewParameter('inlist')) {
            $oArticle->setInList();
        }
        if ($iLinkType) {
            $oArticle->setLinkType($iLinkType);
        }
        // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
        if ($oRecommList = $this->getActiveRecommList()) {
            $oArticle->text = $oRecommList->getArtDescription($oArticle->getId());
        }
        // END deprecated

        return $oArticle;
    }

    /**
     * @param string $dynamicParameters
     *
     * @return string
     */
    protected function updateDynamicParameters($dynamicParameters)
    {
        return $dynamicParameters;
    }
}
