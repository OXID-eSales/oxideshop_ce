<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
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
     * If user is not logged in - returns name of template AccountOrderController::_sThisLoginTemplate, or if user is
     * already logged in - returns name of template AccountOrderController::_sThisTemplate
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
            $this->_aOrderList = [];

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
        $aPaths = [];
        $aPath = [];
        $iBaseLanguage = Registry::getLang()->getBaseLanguage();
        $sSelfLink = $this->getViewConfig()->getSelfLink();

        $aPath['title'] = Registry::getLang()->translateString('MY_ACCOUNT', $iBaseLanguage, false);
        $aPath['link'] = Registry::getSeoEncoder()->getStaticUrl($sSelfLink . 'cl=account');
        $aPaths[] = $aPath;

        $aPath['title'] = Registry::getLang()->translateString('ORDER_HISTORY', $iBaseLanguage, false);
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
