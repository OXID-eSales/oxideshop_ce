<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
use oxRegistry;
use oxUBase;
use oxBasket;
use oxOrder;

/**
 * Thankyou page.
 * Arranges Thankyou page, sets ordering status, other parameters
 */
class ThankYouController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * User basket object
     *
     * @var object
     */
    protected $_oBasket = null;

    /**
     * List of customer also bought thies products
     *
     * @var object
     */
    protected $_aLastProducts = null;

    /**
     * Currency conversion index value
     *
     * @var double
     */
    protected $_dConvIndex = null;

    /**
     * IPayment basket
     *
     * @var double
     */
    protected $_dIPaymentBasket = null;

    /**
     * IPayment account
     *
     * @var string
     */
    protected $_sIPaymentAccount = null;

    /**
     * IPayment user name
     *
     * @var string
     */
    protected $_sIPaymentUser = null;

    /**
     * IPayment password
     *
     * @var string
     */
    protected $_sIPaymentPassword = null;

    /**
     * Mail error
     *
     * @var string
     */
    protected $_sMailError = null;

    /**
     * Sign if to load and show bargain action
     *
     * @var bool
     */
    protected $_blBargainAction = true;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/checkout/thankyou.tpl';

    /**
     * Executes parent::init(), loads basket from session
     * (thankyou::_oBasket = \OxidEsales\Eshop\Core\Session::getBasket()) then destroys
     * it (\OxidEsales\Eshop\Core\Session::delBasket()), unsets user session ID, if
     * this user didn't entered password while ordering.
     */
    public function init()
    {
        parent::init();

        // get basket we might need some information from it here
        $oBasket = $this->getSession()->getBasket();
        $oBasket->setOrderId(Registry::getSession()->getVariable('sess_challenge'));

        // copying basket object
        $this->_oBasket = clone $oBasket;

        // delete it from the session
        $oBasket->deleteBasket();
        Registry::getSession()->deleteVariable('sess_challenge');
        
        // if not in order-context, redirect to start
        $order = $this->getOrder();
        if (!$order || !$order->getFieldData('oxordernr')) {
            Registry::getUtils()->redirect($this->getConfig()->getShopHomeURL() . '&cl=start');
        }
    }

    /**
     * First checks for basket - if no such object available -
     * redirects to start page. Otherwise - executes parent::render()
     * and returns name of template to render thankyou::_sThisTemplate.
     *
     * @return  string  current template file name
     */
    public function render()
    {
        if (!$this->_oBasket || !$this->_oBasket->getProductsCount()) {
            Registry::getUtils()->redirect($this->getConfig()->getShopHomeUrl() . '&cl=start', true, 302);
        }

        parent::render();

        $oUser = $this->getUser();

        // removing also unregistered user info (#2580)
        if (!$oUser || !$oUser->oxuser__oxpassword->value) {
            Registry::getSession()->deleteVariable('usr');
            Registry::getSession()->deleteVariable('dynvalue');
        }

        // loading order sometimes needed in template
        if ($this->_oBasket->getOrderId()) {
            // owners stock reminder
            $oEmail = oxNew(\OxidEsales\Eshop\Core\Email::class);
            $oEmail->sendStockReminder($this->_oBasket->getContents());
        }

        // we must set active class as start
        $this->getViewConfig()->setViewConfigParam('cl', 'start');

        return $this->_sThisTemplate;
    }

    /**
     * Template variable getter. Returns active basket
     *
     * @return oxBasket
     */
    public function getBasket()
    {
        return $this->_oBasket;
    }

    /**
     * Template variable getter. Returns list of customer also bought these products
     *
     * @return object
     */
    public function getAlsoBoughtTheseProducts()
    {
        if ($this->_aLastProducts === null) {
            $this->_aLastProducts = false;
            // 5th order step
            $aBasketContents = array_values($this->getBasket()->getContents());
            if ($oBasketItem = $aBasketContents[0]) {
                if ($oProduct = $oBasketItem->getArticle(false)) {
                    $this->_aLastProducts = $oProduct->getCustomerAlsoBoughtThisProducts();
                }
            }
        }

        return $this->_aLastProducts;
    }

    /**
     * Template variable getter. Returns currency conversion index value
     *
     * @return object
     */
    public function getCurrencyCovIndex()
    {
        if ($this->_dConvIndex === null) {
            // currency conversion index value
            $oCur = $this->getConfig()->getActShopCurrencyObject();
            $this->_dConvIndex = 1 / $oCur->rate;
        }

        return $this->_dConvIndex;
    }

    /**
     * Template variable getter. Returns ipayment basket price
     *
     * @return double
     */
    public function getIPaymentBasket()
    {
        if ($this->_dIPaymentBasket === null) {
            $this->_dIPaymentBasket = $this->getBasket()->getPrice()->getBruttoPrice() * 100;
        }

        return $this->_dIPaymentBasket;
    }

    /**
     * Template variable getter. Returns ipayment account
     *
     * @return string
     */
    public function getIPaymentAccount()
    {
        if ($this->_sIPaymentAccount === null) {
            $this->_sIPaymentAccount = false;
            $this->_sIPaymentAccount = $this->getConfig()->getConfigParam('iShopID_iPayment_Account');
        }

        return $this->_sIPaymentAccount;
    }

    /**
     * Template variable getter. Returns ipayment user name
     *
     * @return string
     */
    public function getIPaymentUser()
    {
        if ($this->_sIPaymentUser === null) {
            $this->_sIPaymentUser = false;
            $this->_sIPaymentUser = $this->getConfig()->getConfigParam('iShopID_iPayment_User');
        }

        return $this->_sIPaymentUser;
    }

    /**
     * Template variable getter. Returns ipayment password
     *
     * @return string
     */
    public function getIPaymentPassword()
    {
        if ($this->_sIPaymentPassword === null) {
            $this->_sIPaymentPassword = false;
            $this->_sIPaymentPassword = $this->getConfig()->getConfigParam('iShopID_iPayment_Passwort');
        }

        return $this->_sIPaymentPassword;
    }

    /**
     * Template variable getter. Returns mail error
     *
     * @return string
     */
    public function getMailError()
    {
        if ($this->_sMailError === null) {
            $this->_sMailError = false;
            $this->_sMailError = Registry::getConfig()->getRequestParameter('mailerror');
        }

        return $this->_sMailError;
    }

    /**
     * Template variable getter. Returns order
     *
     * @return oxOrder
     */
    public function getOrder()
    {
        if ($this->_oOrder === null) {
            $this->_oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            // loading order sometimes needed in template
            if ($sOrderId = $this->getBasket()->getOrderId()) {
                $this->_oOrder->load($sOrderId);
            }
        }

        return $this->_oOrder;
    }

    /**
     * Template variable getter. Returns country ISO 3
     *
     * @return string
     */
    public function getCountryISO3()
    {
        $oOrder = $this->getOrder();
        if ($oOrder) {
            $oCountry = oxNew(\OxidEsales\Eshop\Application\Model\Country::class);
            $oCountry->load($oOrder->oxorder__oxbillcountryid->value);

            return $oCountry->oxcountry__oxisoalpha3->value;
        }
    }

    /**
     * Returns name of a view class, which will be active for an action
     * (given a generic fnc, e.g. logout)
     *
     * @return string
     */
    public function getActionClassName()
    {
        return 'start';
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


        $iLang = Registry::getLang()->getBaseLanguage();
        $aPath['title'] = Registry::getLang()->translateString('ORDER_COMPLETED', $iLang, false);
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
