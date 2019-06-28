<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use oxArticle;
use OxidEsales\Eshop\Application\Model\Wrapping;
use OxidEsales\Eshop\Core\Registry;
use oxRegistry;
use oxList;
use oxBasketContentMarkGenerator;
use oxBasket;
use Psr\Log\LoggerInterface;

/**
 * Current session shopping cart (basket item list).
 * Contains with user selected articles (with detail information), list of
 * similar products, top offer articles.
 * OXID eShop -> SHOPPING CART.
 */
class BasketController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/checkout/basket.tpl';

    /**
     * Order step marker
     *
     * @var bool
     */
    protected $_blIsOrderStep = true;

    /**
     * all basket articles
     *
     * @var object
     */
    protected $_oBasketArticles = null;

    /**
     * Similar List
     *
     * @var object
     */
    protected $_oSimilarList = null;

    /**
     * Recomm List
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var object
     */
    protected $_oRecommList = null;

    /**
     * First basket product object. It is used to load
     * recommendation list info and similar product list
     *
     * @var oxArticle
     */
    protected $_oFirstBasketProduct = null;

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Wrapping objects list
     *
     * @var oxList
     */
    protected $_oWrappings = null;

    /**
     * Card objects list
     *
     * @var oxList
     */
    protected $_oCards = null;

    /**
     * Array of id to form recommendation list.
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var array
     */
    protected $_aSimilarRecommListIds = null;

    /**
     * Executes parent::render(), creates list with basket articles
     * Returns name of template file basket::_sThisTemplate (for Search
     * engines return "content.tpl" template to avoid fake orders etc).
     *
     * @return  string   $this->_sThisTemplate  current template file name
     */
    public function render()
    {
        if ($this->getConfig()->getConfigParam('blPsBasketReservationEnabled')) {
            $this->getSession()->getBasketReservations()->renewExpiration();
        }

        parent::render();

        return $this->_sThisTemplate;
    }

    /**
     * Return the current articles from the basket
     *
     * @return object | bool
     */
    public function getBasketArticles()
    {
        if ($this->_oBasketArticles === null) {
            $this->_oBasketArticles = false;

            // passing basket articles
            if ($oBasket = $this->getSession()->getBasket()) {
                $this->_oBasketArticles = $oBasket->getBasketArticles();
            }
        }

        return $this->_oBasketArticles;
    }

    /**
     * return the basket articles
     *
     * @return object | bool
     */
    public function getFirstBasketProduct()
    {
        if ($this->_oFirstBasketProduct === null) {
            $this->_oFirstBasketProduct = false;

            $aBasketArticles = $this->getBasketArticles();
            if (is_array($aBasketArticles) && $oProduct = reset($aBasketArticles)) {
                $this->_oFirstBasketProduct = $oProduct;
            }
        }

        return $this->_oFirstBasketProduct;
    }

    /**
     * return the similar articles
     *
     * @return object | bool
     */
    public function getBasketSimilarList()
    {
        if ($this->_oSimilarList === null) {
            $this->_oSimilarList = false;

            // similar product info
            if ($oProduct = $this->getFirstBasketProduct()) {
                $this->_oSimilarList = $oProduct->getSimilarProducts();
            }
        }

        return $this->_oSimilarList;
    }

    /**
     * Return array of id to form recommend list.
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return array
     */
    public function getSimilarRecommListIds()
    {
        if ($this->_aSimilarRecommListIds === null) {
            $this->_aSimilarRecommListIds = false;

            if ($oProduct = $this->getFirstBasketProduct()) {
                $this->_aSimilarRecommListIds = [$oProduct->getId()];
            }
        }

        return $this->_aSimilarRecommListIds;
    }

    /**
     * return the Link back to shop
     *
     * @return bool
     */
    public function showBackToShop()
    {
        $iNewBasketItemMessage = $this->getConfig()->getConfigParam('iNewBasketItemMessage');
        $sBackToShop = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('_backtoshop');

        return ($iNewBasketItemMessage == 3 && $sBackToShop);
    }

    /**
     * Assigns voucher to current basket
     *
     * @return null
     */
    public function addVoucher()
    {
        if (!Registry::getSession()->checkSessionChallenge()) {
            $this->getContainer()->get(LoggerInterface::class)->warning('EXCEPTION_NON_MATCHING_CSRF_TOKEN');
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_NON_MATCHING_CSRF_TOKEN');
            return;
        }

        if (!$this->getViewConfig()->getShowVouchers()) {
            return;
        }

        $oBasket = $this->getSession()->getBasket();
        $oBasket->addVoucher(Registry::getConfig()->getRequestParameter('voucherNr'));
    }

    /**
     * Removes voucher from basket (calls \OxidEsales\Eshop\Application\Model\Basket::removeVoucher())
     *
     * @return null
     */
    public function removeVoucher()
    {
        if (!Registry::getSession()->checkSessionChallenge()) {
            $this->getContainer()->get(LoggerInterface::class)->warning('EXCEPTION_NON_MATCHING_CSRF_TOKEN');
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_NON_MATCHING_CSRF_TOKEN');
            return;
        }

        if (!$this->getViewConfig()->getShowVouchers()) {
            return;
        }

        $oBasket = $this->getSession()->getBasket();
        $oBasket->removeVoucher(Registry::getConfig()->getRequestParameter('voucherId'));
    }

    /**
     * Redirects user back to previous part of shop (list, details, ...) from basket.
     * Used with option "Display Message when Product is added to Cart" set to "Open Basket"
     * ($myConfig->iNewBasketItemMessage == 3)
     *
     * @return string   $sBackLink  back link
     */
    public function backToShop()
    {
        if ($this->getConfig()->getConfigParam('iNewBasketItemMessage') == 3) {
            $oSession = \OxidEsales\Eshop\Core\Registry::getSession();
            if ($sBackLink = $oSession->getVariable('_backtoshop')) {
                $oSession->deleteVariable('_backtoshop');

                return $sBackLink;
            }
        }
    }

    /**
     * Returns a name of the view variable containing the error/exception messages
     *
     * @return null
     */
    public function getErrorDestination()
    {
        return 'basket';
    }

    /**
     * Returns wrapping options availability state (TRUE/FALSE)
     *
     * @return bool
     */
    public function isWrapping()
    {
        if (!$this->getViewConfig()->getShowGiftWrapping()) {
            return false;
        }

        if ($this->_iWrapCnt === null) {
            $this->_iWrapCnt = 0;

            $oWrap = oxNew(Wrapping::class);
            $this->_iWrapCnt += $oWrap->getWrappingCount('WRAP');
            $this->_iWrapCnt += $oWrap->getWrappingCount('CARD');
        }

        return (bool) $this->_iWrapCnt;
    }

    /**
     * Return basket wrappings list if available
     *
     * @return oxlist
     */
    public function getWrappingList()
    {
        if ($this->_oWrappings === null) {
            $this->_oWrappings = new \OxidEsales\Eshop\Core\Model\ListModel();

            // load wrapping papers
            if ($this->getViewConfig()->getShowGiftWrapping()) {
                $this->_oWrappings = oxNew(Wrapping::class)->getWrappingList('WRAP');
            }
        }

        return $this->_oWrappings;
    }

    /**
     * Returns greeting cards list if available
     *
     * @return oxlist
     */
    public function getCardList()
    {
        if ($this->_oCards === null) {
            $this->_oCards = new \OxidEsales\Eshop\Core\Model\ListModel();

            // load gift cards
            if ($this->getViewConfig()->getShowGiftWrapping()) {
                $this->_oCards = oxNew(Wrapping::class)->getWrappingList('CARD');
            }
        }

        return $this->_oCards;
    }

    /**
     * Updates wrapping data in session basket object
     * (\OxidEsales\Eshop\Core\Session::getBasket()) - adds wrapping info to
     * each article in basket (if possible). Plus adds
     * gift message and chosen card ( takes from GET/POST/session;
     * oBasket::giftmessage, oBasket::chosencard). Then sets
     * basket back to session (\OxidEsales\Eshop\Core\Session::setBasket()).
     */
    public function changeWrapping()
    {
        if (!Registry::getSession()->checkSessionChallenge()) {
            $this->getContainer()->get(LoggerInterface::class)->warning('EXCEPTION_NON_MATCHING_CSRF_TOKEN');
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_NON_MATCHING_CSRF_TOKEN');
            return;
        }

        $oConfig = Registry::getConfig();

        if ($this->getViewConfig()->getShowGiftWrapping()) {
            $oBasket = $this->getSession()->getBasket();

            $this->_setWrappingInfo($oBasket, $oConfig->getRequestParameter('wrapping'));

            $oBasket->setCardMessage($oConfig->getRequestParameter('giftmessage'));
            $oBasket->setCardId($oConfig->getRequestParameter('chosencard'));
            $oBasket->onUpdate();
        }
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
        $aPath['title'] = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('CART', $iBaseLanguage, false);
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Method returns object with explanation marks for articles in basket.
     *
     * @return \OxidEsales\Eshop\Application\Model\BasketContentMarkGenerator
     */
    public function getBasketContentMarkGenerator()
    {
        /** @var \OxidEsales\Eshop\Application\Model\BasketContentMarkGenerator $oBasketContentMarkGenerator */
        return oxNew('oxBasketContentMarkGenerator', $this->getSession()->getBasket());
    }

    /**
     * Sets basket wrapping
     *
     * @param \OxidEsales\Eshop\Application\Model\Basket $oBasket
     * @param array                                      $aWrapping
     */
    protected function _setWrappingInfo($oBasket, $aWrapping)
    {
        if (is_array($aWrapping) && count($aWrapping)) {
            foreach ($oBasket->getContents() as $sKey => $oBasketItem) {
                if (isset($aWrapping[$sKey])) {
                    $oBasketItem->setWrapping($aWrapping[$sKey]);
                }
            }
        }
    }
}
