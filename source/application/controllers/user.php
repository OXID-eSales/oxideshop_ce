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
 * User details.
 * Collects and arranges user object data (information, like shipping address, etc.).
 */
class User extends oxUBase
{
    /**
     * Current class template.
     * @var string
     */
    protected $_sThisTemplate = 'page/checkout/user.tpl';

    /**
     * Order step marker
     * @var bool
     */
    protected $_blIsOrderStep = true;

    /**
     * Revers of option blOrderDisWithoutReg
     * @var array
     */
    protected $_blShowNoRegOpt = null;

    /**
     * Selected Address
     * @var object
     */
    protected $_sSelectedAddress = null;

    /**
     * Login option
     * @var integer
     */
    protected $_iOption = null;

    /**
     * Country list
     * @var object
     */
    protected $_oCountryList = null;

    /**
     * Order remark
     * @var string
     */
    protected $_sOrderRemark = null;

    /**
     * Wishlist user id
     * @var string
     */
    protected $_sWishId = null;


    /**
     * Loads customer basket object form session (oxsession::getBasket()),
     * passes action article/basket/country list to template engine. If
     * available - loads user delivery address data (oxaddress). If user
     * is connected using Facebook connect calls user::_fillFormWithFacebookData to
     * prefill form data with data taken from user Facebook account. Returns
     * name template file to render user::_sThisTemplate.
     *
     * @return  string  $this->_sThisTemplate   current template file name
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        if ( $this->getIsOrderStep() ) {
            if ($myConfig->getConfigParam( 'blPsBasketReservationEnabled' )) {
                $this->getSession()->getBasketReservations()->renewExpiration();
            }

            $oBasket = $this->getSession()->getBasket();
            if ( $this->_blIsOrderStep && $myConfig->getConfigParam( 'blPsBasketReservationEnabled' ) && (!$oBasket || ( $oBasket && !$oBasket->getProductsCount() )) ) {
                oxRegistry::getUtils()->redirect( $myConfig->getShopHomeURL() .'cl=basket', true, 302 );
            }
        }

        parent::render();

        if ( $myConfig->getConfigParam( "bl_showFbConnect" ) && !$this->getUser() ) {
             $this->_fillFormWithFacebookData();
        }

        return $this->_sThisTemplate;
    }

    /**
     * Template variable getter. Returns reverse option blOrderDisWithoutReg
     *
     * @return bool
     */
    public function getShowNoRegOption()
    {
        if ( $this->_blShowNoRegOpt === null ) {
            $this->_blShowNoRegOpt = !$this->getConfig()->getConfigParam( 'blOrderDisWithoutReg' );
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
        if ( $this->_iOption === null ) {
            // passing user chosen option value to display correct content
            $iOption = oxConfig::getParameter( 'option' );
            // if user chosen "Option 2"" - we should show user details only if he is authorized
            if ( $iOption == 2 && !$this->getUser() ) {
                $iOption = 0;
            }
            $this->_iOption = $iOption;
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
        if ( $this->_sOrderRemark === null ) {
            $sOrderRemark = false;
            // if already connected, we can use the session
            if ( $this->getUser() ) {
                $sOrderRemark = oxSession::getVar( 'ordrem' );
            } else {
                // not connected so nowhere to save, we're gonna use what we get from post
                $sOrderRemark = oxConfig::getParameter( 'order_remark', true );
            }

            $this->_sOrderRemark = $sOrderRemark ? oxRegistry::getConfig()->checkParamSpecialChars( $sOrderRemark ) : false;
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
        if ( $this->_blNewsSubscribed === null ) {
            $blNews = false;
            if ( ( $blNews = oxConfig::getParameter( 'blnewssubscribed' ) ) === null ) {
                $blNews = false;
            }
            if ( ( $oUser = $this->getUser() ) ) {
                $blNews = $oUser->getNewsSubscription()->getOptInStatus();
            }
            $this->_blNewsSubscribed = $blNews;
        }

        if (is_null($this->_blNewsSubscribed))
            $this->_blNewsSubscribed = false;

        return  $this->_blNewsSubscribed;
    }

    /**
     * Template variable getter. Checks to show or not shipping address entry form
     *
     * @return bool
     */
    public function showShipAddress()
    {
        return oxSession::getVar( 'blshowshipaddress' );
    }

    /**
     * Fills user form with date taken from Facebook
     *
     * @return null
     */
    protected function _fillFormWithFacebookData()
    {
        // Create our Application instance.
        $oFacebook = oxRegistry::get("oxFb");

        if ( $oFacebook->isConnected() ) {
            $aMe  = $oFacebook->api('/me');

            $aInvAdr = $this->getInvoiceAddress();
            $sCharset = oxRegistry::getLang()->translateString( "charset" );

            // do not stop converting on error - just try to translit unknown symbols
            $sCharset .= '//TRANSLIT';

            if ( !$aInvAdr["oxuser__oxfname"] ) {
                $aInvAdr["oxuser__oxfname"] = iconv( 'UTF-8', $sCharset, $aMe["first_name"] );
            }

            if ( !$aInvAdr["oxuser__oxlname"] ) {
                $aInvAdr["oxuser__oxlname"] = iconv( 'UTF-8', $sCharset, $aMe["last_name"] );
            }

            $this->setInvoiceAddress( $aInvAdr );
        }
    }

    /**
     * Return true if user wants to change his billing address
     *
     * @return bool
     */
    public function modifyBillAddress()
    {
        return oxConfig::getParameter( 'blnewssubscribed' );
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

        $aPath['title'] = oxRegistry::getLang()->translateString( 'ADDRESS', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = $this->getLink();

        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Returns warning message if user want to buy downloadable product without registration.
     *
     * @return bool
     */
    public function isDownloadableProductWarning()
    {
        $oBasket = $this->getSession()->getBasket();
        if ( $oBasket && $this->getConfig()->getConfigParam( "blEnableDownloads" ) ) {
            if ( $oBasket->hasDownloadableProducts() ) {
                return true;
            }
        }
        return false;
    }
}
