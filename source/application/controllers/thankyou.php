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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Thankyou page.
 * Arranges Thankyou page, sets ordering status, other parameters
 */
class Thankyou extends oxUBase
{
    /**
     * User basket object
     * @var object
     */
    protected $_oBasket = null;

    /**
     * List of customer also bought thies products
     * @var object
     */
    protected $_aLastProducts = null;

    /**
     * Currency conversion index value
     * @var double
     */
    protected $_dConvIndex = null;

    /**
     * IPayment basket
     * @var double
     */
    protected $_dIPaymentBasket = null;

    /**
     * IPayment account
     * @var string
     */
    protected $_sIPaymentAccount = null;

    /**
     * IPayment user name
     * @var string
     */
    protected $_sIPaymentUser = null;

    /**
     * IPayment password
     * @var string
     */
    protected $_sIPaymentPassword = null;

    /**
     * Mail error
     * @var string
     */
    protected $_sMailError = null;

    /**
     * Sign if to load and show bargain action
     * @var bool
     */
    protected $_blBargainAction = true;


    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'page/checkout/thankyou.tpl';

    /**
     * Executes parent::init(), loads basket from session
     * (thankyou::_oBasket = oxsession::getBasket()) then destroys
     * it (oxsession::delBasket()), unsets user session ID, if
     * this user didn't entered password while ordering.
     *
     * @return null
     */
    public function init()
    {
        parent::init();

        // get basket we might need some information from it here
        $oBasket = $this->getSession()->getBasket();
        $oBasket->setOrderId( oxSession::getVar( 'sess_challenge' ) );

        // copying basket object
        $this->_oBasket = clone $oBasket;

        // delete it from the session
        $oBasket->deleteBasket();
        oxSession::deleteVar( 'sess_challenge' );
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
        if ( !$this->_oBasket || !$this->_oBasket->getProductsCount() ) {
            oxRegistry::getUtils()->redirect( $this->getConfig()->getShopHomeURL().'&cl=start', true, 302 );
        }

        parent::render();

        $oUser = $this->getUser();

        // removing also unregistered user info (#2580)
        if ( !$oUser || !$oUser->oxuser__oxpassword->value) {
            oxSession::deleteVar( 'usr' );
            oxSession::deleteVar( 'dynvalue' );
        }

        // loading order sometimes needed in template
        if ( $this->_oBasket->getOrderId() ) {
            // owners stock reminder
            $oEmail = oxNew( 'oxemail' );
            $oEmail->sendStockReminder( $this->_oBasket->getContents() );
        }

        // we must set active class as start
        $this->getViewConfig()->setViewConfigParam( 'cl', 'start' );

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
     * Template variable getter. Returns if to show final (5th) step
     *
     * @deprecated since v5.0.1 (2012-11-19). Option blShowFinalStep is removed
     *
     * @return string
     */
    public function showFinalStep()
    {
        return true;
    }

    /**
     * Template variable getter. Returns list of customer also bought thies products
     *
     * @return object
     */
    public function getAlsoBoughtTheseProducts()
    {
        if ( $this->_aLastProducts === null ) {
            $this->_aLastProducts = false;
            // 5th order step
            $aBasketContents = array_values($this->getBasket()->getContents());
            if ( $oBasketItem = $aBasketContents[0] ) {
                if ( $oProduct = $oBasketItem->getArticle(false) ) {
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
        if ( $this->_dConvIndex === null ) {
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
        if ( $this->_dIPaymentBasket === null ) {
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
        if ( $this->_sIPaymentAccount === null ) {
            $this->_sIPaymentAccount = false;
            $this->_sIPaymentAccount = $this->getConfig()->getConfigParam( 'iShopID_iPayment_Account' );
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
        if ( $this->_sIPaymentUser === null ) {
            $this->_sIPaymentUser = false;
            $this->_sIPaymentUser = $this->getConfig()->getConfigParam( 'iShopID_iPayment_User' );
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
        if ( $this->_sIPaymentPassword === null ) {
            $this->_sIPaymentPassword = false;
            $this->_sIPaymentPassword = $this->getConfig()->getConfigParam( 'iShopID_iPayment_Passwort' );
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
        if ( $this->_sMailError === null ) {
            $this->_sMailError = false;
            $this->_sMailError = oxConfig::getParameter( 'mailerror' );
        }
        return $this->_sMailError;
    }

    /**
     * Template variable getter. Returns mail error
     *
     * @return oxOrder
     */
    public function getOrder()
    {
        if ( $this->_oOrder === null ) {
            $this->_oOrder = oxNew( 'oxorder' );
            // loading order sometimes needed in template
            if ( $sOrderId = $this->getBasket()->getOrderId() ) {
                $this->_oOrder->load( $sOrderId );
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
        if ( $oOrder ) {
            $oCountry = oxNew( 'oxcountry' );
            $oCountry->load( $oOrder->oxorder__oxbillcountryid->value );
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
        $aPaths = array();
        $aPath = array();


        $aPath['title'] = oxRegistry::getLang()->translateString( 'ORDER_COMPLETED', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
