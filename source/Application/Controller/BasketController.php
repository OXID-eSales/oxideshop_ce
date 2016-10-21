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

use oxArticle;
use oxRegistry;
use oxList;
use oxBasketContentMarkGenerator;
use oxBasket;

/**
 * Current session shopping cart (basket item list).
 * Contains with user selected articles (with detail information), list of
 * similar products, top offer articles.
 * OXID eShop -> SHOPPING CART.
 */
class BasketController extends \oxUBase
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
                $this->_aSimilarRecommListIds = array($oProduct->getId());
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
        $sBackToShop = oxRegistry::getSession()->getVariable('_backtoshop');

        return ($iNewBasketItemMessage == 3 && $sBackToShop);
    }

    /**
     * Assigns voucher to current basket
     *
     * @return null
     */
    public function addVoucher()
    {
        if (!$this->getViewConfig()->getShowVouchers()) {
            return;
        }

        $oBasket = $this->getSession()->getBasket();
        $oBasket->addVoucher(oxRegistry::getConfig()->getRequestParameter('voucherNr'));
    }

    /**
     * Removes voucher from basket (calls oxBasket::removeVoucher())
     *
     * @return null
     */
    public function removeVoucher()
    {
        if (!$this->getViewConfig()->getShowVouchers()) {
            return;
        }

        $oBasket = $this->getSession()->getBasket();
        $oBasket->removeVoucher(oxRegistry::getConfig()->getRequestParameter('voucherId'));
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
            $oSession = oxRegistry::getSession();
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

            $oWrap = oxNew('oxwrapping');
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
            $this->_oWrappings = new oxlist();

            // load wrapping papers
            if ($this->getViewConfig()->getShowGiftWrapping()) {
                $this->_oWrappings = oxNew('oxwrapping')->getWrappingList('WRAP');
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
            $this->_oCards = new oxlist();

            // load gift cards
            if ($this->getViewConfig()->getShowGiftWrapping()) {
                $this->_oCards = oxNew('oxwrapping')->getWrappingList('CARD');
            }
        }

        return $this->_oCards;
    }

    /**
     * Updates wrapping data in session basket object
     * (oxSession::getBasket()) - adds wrapping info to
     * each article in basket (if possible). Plus adds
     * gift message and chosen card ( takes from GET/POST/session;
     * oBasket::giftmessage, oBasket::chosencard). Then sets
     * basket back to session (oxSession::setBasket()).
     */
    public function changeWrapping()
    {
        $oConfig = oxRegistry::getConfig();

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
        $aPaths = array();
        $aPath = array();

        $iBaseLanguage = oxRegistry::getLang()->getBaseLanguage();
        $aPath['title'] = oxRegistry::getLang()->translateString('CART', $iBaseLanguage, false);
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Method returns object with explanation marks for articles in basket.
     *
     * @return oxBasketContentMarkGenerator
     */
    public function getBasketContentMarkGenerator()
    {
        /** @var oxBasketContentMarkGenerator $oBasketContentMarkGenerator */
        return oxNew('oxBasketContentMarkGenerator', $this->getSession()->getBasket());
    }

    /**
     * Sets basket wrapping
     *
     * @param oxBasket $oBasket
     * @param array    $aWrapping
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
