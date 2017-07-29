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
 * Current user order history review.
 * When user is logged in order review fulfils history about user
 * submitted orders. There is some details information, such as
 * ordering date, number, recipient, order status, some base
 * ordered articles information, button to add article to basket.
 * OXID eShop -> MY ACCOUNT -> Newsletter.
 */
class AccountOrderController extends \OxidEsales\Eshop\Application\Controller\AccountController
{

    /**
     * Count of all articles in list.
     *
     * @var integer
     */
    protected $_iAllArtCnt = 0;

    /**
     * Number of possible pages.
     *
     * @var integer
     */
    protected $_iCntPages = null;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/account/order.tpl';

    /**
     * collecting orders
     *
     * @var array
     */
    protected $_aOrderList = null;

    /**
     * collecting article which ordered
     *
     * @var array
     */
    protected $_aArticlesList = null;

    /**
     * If user is not logged in - returns name of template \OxidEsales\Eshop\Application\Controller\AccountOrderController::_sThisLoginTemplate,
     * or if user is allready logged in - returns name of template
     * \OxidEsales\Eshop\Application\Controller\AccountOrderController::_sThisTemplate
     *
     * @return string $_sThisTemplate current template file name
     */
    public function render()
    {
        parent::render();

        // is logged in ?
        $oUser = $this->getUser();
        if (!$oUser) {
            return $this->_sThisTemplate = $this->_sThisLoginTemplate;
        }

        return $this->_sThisTemplate;
    }

    /**
     * Template variable getter. Returns orders
     *
     * @return array
     */
    public function getOrderList()
    {
        if ($this->_aOrderList === null) {
            $this->_aOrderList = array();

            // Load user Orderlist
            if ($oUser = $this->getUser()) {
                $iNrofCatArticles = (int) $this->getConfig()->getConfigParam('iNrofCatArticles');
                $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 1;
                $this->_iAllArtCnt = $oUser->getOrderCount();
                if ($this->_iAllArtCnt && $this->_iAllArtCnt > 0) {
                    $this->_aOrderList = $oUser->getOrders($iNrofCatArticles, $this->getActPage());
                    $this->_iCntPages = ceil($this->_iAllArtCnt / $iNrofCatArticles);
                }
            }
        }

        return $this->_aOrderList;
    }

    /**
     * Template variable getter. Returns ordered articles
     *
     * @return \OxidEsales\Eshop\Application\Model\ArticleList | false
     */
    public function getOrderArticleList()
    {
        if ($this->_aArticlesList === null) {
            // marking as set
            $this->_aArticlesList = false;
            $oOrdersList = $this->getOrderList();
            if ($oOrdersList && $oOrdersList->count()) {
                $this->_aArticlesList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
                $this->_aArticlesList->loadOrderArticles($oOrdersList);
            }
        }

        return $this->_aArticlesList;
    }

    /**
     * Template variable getter. Returns page navigation
     *
     * @return object
     */
    public function getPageNavigation()
    {
        if ($this->_oPageNavigation === null) {
            $this->_oPageNavigation = $this->generatePageNavigation();
        }

        return $this->_oPageNavigation;
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
        $iBaseLanguage = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $sSelfLink = $this->getViewConfig()->getSelfLink();

        $aPath['title'] = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('MY_ACCOUNT', $iBaseLanguage, false);
        $aPath['link'] = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->getStaticUrl($sSelfLink . 'cl=account');
        $aPaths[] = $aPath;

        $aPath['title'] = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('ORDER_HISTORY', $iBaseLanguage, false);
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
