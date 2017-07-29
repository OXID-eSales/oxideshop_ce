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

use oxList;
use oxRegistry;
use oxUBase;

/**
 * Managing Gift Wrapping
 */
class WrappingController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/checkout/wrapping.tpl';

    /**
     * Basket items array
     *
     * @var array
     */
    protected $_aBasketItemList = null;

    /**
     * Wrapping objects list
     *
     * @var oxlist
     */
    protected $_oWrappings = null;

    /**
     * Card objects list
     *
     * @var oxlist
     */
    protected $_oCards = null;

    /**
     * Returns array of shopping basket articles
     *
     * @return array
     */
    public function getBasketItems()
    {
        if ($this->_aBasketItemList === null) {
            $this->_aBasketItemList = false;

            // passing basket articles
            if ($oBasket = $this->getSession()->getBasket()) {
                $this->_aBasketItemList = $oBasket->getBasketArticles();
            }
        }

        return $this->_aBasketItemList;
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
                $this->_oWrappings = oxNew(\OxidEsales\Eshop\Application\Model\Wrapping::class)->getWrappingList('WRAP');
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
                $this->_oCards = oxNew(\OxidEsales\Eshop\Application\Model\Wrapping::class)->getWrappingList('CARD');
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
     * basket back to session (\OxidEsales\Eshop\Core\Session::setBasket()). Returns
     * "order" to redirect to order confirmation secreen.
     *
     * @return string
     */
    public function changeWrapping()
    {
        $aWrapping = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('wrapping');

        if ($this->getViewConfig()->getShowGiftWrapping()) {
            $oBasket = $this->getSession()->getBasket();
            // setting wrapping info
            if (is_array($aWrapping) && count($aWrapping)) {
                foreach ($oBasket->getContents() as $sKey => $oBasketItem) {
                    // wrapping ?
                    if (isset($aWrapping[$sKey])) {
                        $oBasketItem->setWrapping($aWrapping[$sKey]);
                    }
                }
            }

            $oBasket->setCardMessage(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('giftmessage'));
            $oBasket->setCardId(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('chosencard'));
            $oBasket->onUpdate();
        }

        return 'order';
    }
}
