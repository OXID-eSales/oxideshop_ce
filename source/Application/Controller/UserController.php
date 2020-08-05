<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;

/**
 * User details.
 * Collects and arranges user object data (information, like shipping address, etc.).
 */
class UserController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Current class template.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/checkout/user';

    /**
     * Order step marker
     *
     * @var bool
     */
    protected $_blIsOrderStep = true;

    /**
     * Revers of option blOrderDisWithoutReg
     *
     * @var array
     */
    protected $_blShowNoRegOpt = null;

    /**
     * Selected Address
     *
     * @var object
     */
    protected $_sSelectedAddress = null;

    /**
     * Login option
     *
     * @var integer
     */
    protected $_iOption = null;

    /**
     * Country list
     *
     * @var object
     */
    protected $_oCountryList = null;

    /**
     * Order remark
     *
     * @var string
     */
    protected $_sOrderRemark = null;

    /**
     * Wishlist user id
     *
     * @var string
     */
    protected $_sWishId = null;

    /**
     * Loads customer basket object form session (\OxidEsales\Eshop\Core\Session::getBasket()),
     * passes action article/basket/country list to template engine. If
     * available - loads user delivery address data (oxAddress). Returns
     * name template file to render user::_sThisTemplate.
     *
     * @return  string  $this->_sThisTemplate   current template file name
     */
    public function render()
    {
        $config = Registry::getConfig();

        if ($this->getIsOrderStep()) {
            $session = Registry::getSession();

            if ($config->getConfigParam('blPsBasketReservationEnabled')) {
                $session->getBasketReservations()->renewExpiration();
            }

            $basket = $session->getBasket();
            $isPsBasketReservationsEnabled = $config->getConfigParam('blPsBasketReservationEnabled');
            if (
                $this->_blIsOrderStep && $isPsBasketReservationsEnabled &&
                (!$basket || ($basket && !$basket->getProductsCount()))
            ) {
                Registry::getUtils()->redirect($config->getShopHomeUrl() . 'cl=basket', true, 302);
            }
        }

        $this->_aViewData["deladr"] = Registry::getRequest()->getRequestEscapedParameter('deladr');

        parent::render();

        return $this->_sThisTemplate;
    }

    /**
     * Template variable getter. Returns reverse option blOrderDisWithoutReg
     *
     * @return bool
     */
    public function getShowNoRegOption()
    {
        if ($this->_blShowNoRegOpt === null) {
            $this->_blShowNoRegOpt = !Registry::getConfig()->getConfigParam('blOrderDisWithoutReg');
        }

        return $this->_blShowNoRegOpt;
    }

    /**
     * Template variable getter. Returns user login option
     *
     * @return integer
     */
    public function getLoginOption()
    {
        if ($this->_iOption === null) {
            // passing user chosen option value to display correct content
            $option = Registry::getRequest()->getRequestEscapedParameter('option');
            // if user chosen "Option 2"" - we should show user details only if he is authorized
            if ($option == 2 && !$this->getUser()) {
                $option = 0;
            }
            $this->_iOption = $option;
        }

        return $this->_iOption;
    }

    /**
     * Template variable getter. Returns order remark
     *
     * @return string
     */
    public function getOrderRemark()
    {
        $config = Registry::getConfig();
        if ($this->_sOrderRemark === null) {
            // if already connected, we can use the session
            if ($this->getUser()) {
                $orderRemark = Registry::getSession()->getVariable('ordrem');
            } else {
                // not connected so nowhere to save, we're gonna use what we get from post
                $orderRemark = Registry::getRequest()->getRequestParameter('order_remark');
            }

            $this->_sOrderRemark = $orderRemark ? $config->checkParamSpecialChars($orderRemark) : false;
        }

        return $this->_sOrderRemark;
    }

    /**
     * Template variable getter. Returns if user subscribed for newsletter
     *
     * @return bool
     */
    public function isNewsSubscribed()
    {
        if ($this->_blNewsSubscribed === null) {
            if (($isSubscribedToNews = Registry::getRequest()->getRequestEscapedParameter('blnewssubscribed')) === null) {
                $isSubscribedToNews = false;
            }
            if (($user = $this->getUser())) {
                $isSubscribedToNews = $user->getNewsSubscription()->getOptInStatus();
            }
            $this->_blNewsSubscribed = $isSubscribedToNews;
        }

        if (is_null($this->_blNewsSubscribed)) {
            $this->_blNewsSubscribed = false;
        }

        return $this->_blNewsSubscribed;
    }

    /**
     * Template variable getter. Checks to show or not shipping address entry form
     *
     * @return bool
     */
    public function showShipAddress()
    {
        return Registry::getSession()->getVariable('blshowshipaddress');
    }

    /**
     * Return true if user wants to change his billing address
     *
     * @return bool
     */
    public function modifyBillAddress()
    {
        return Registry::getRequest()->getRequestEscapedParameter('blnewssubscribed');
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $paths = [];
        $path = [];

        $baseLanguageId = Registry::getLang()->getBaseLanguage();
        $path['title'] = Registry::getLang()->translateString('ADDRESS', $baseLanguageId, false);
        $path['link'] = $this->getLink();

        $paths[] = $path;

        return $paths;
    }

    /**
     * Returns warning message if user want to buy downloadable product without registration.
     *
     * @return bool
     */
    public function isDownloadableProductWarning()
    {
        $session = Registry::getSession();
        $basket = $session->getBasket();
        if ($basket && Registry::getConfig()->getConfigParam("blEnableDownloads")) {
            if ($basket->hasDownloadableProducts()) {
                return true;
            }
        }

        return false;
    }
}
