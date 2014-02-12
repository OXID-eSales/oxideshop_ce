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
 * Pricealarm window.
 * Arranges "pricealarm" window, by sending eMail and storing into Database (etc.)
 * submission. Result - "pricealarm.tpl"  template. After user correctly
 * fulfils all required fields all information is sent to shop owner by
 * email.
 * OXID eShop -> pricealarm.
 */
class Pricealarm extends oxUBase
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'pricealarm.tpl';

    /**
     * Current article.
     * @var object
     */
    protected $_oArticle = null;

    /**
     * Bid price.
     * @var string
     */
    protected $_sBidPrice = null;

    /**
     * Price alarm status.
     * @var integer
     */
    protected $_iPriceAlarmStatus = null;

    /**
     * Validates email
     * address. If email is wrong - returns false and exits. If email
     * address is OK - creates prcealarm object and saves it
     * (oxpricealarm::save()). Sends pricealarm notification mail
     * to shop owner.
     *
     * @return  bool    false on error
     */
    public function addme()
    {
        $myConfig = $this->getConfig();
        $myUtils  = oxRegistry::getUtils();

        //control captcha
        $sMac     = oxConfig::getParameter( 'c_mac' );
        $sMacHash = oxConfig::getParameter( 'c_mach' );
        $oCaptcha = oxNew('oxCaptcha');
        if ( !$oCaptcha->pass( $sMac, $sMacHash )) {
            $this->_iPriceAlarmStatus = 2;
            return;
        }

        $aParams = oxConfig::getParameter( 'pa' );
        if ( !isset( $aParams['email'] ) || !$myUtils->isValidEmail( $aParams['email'] ) ) {
            $this->_iPriceAlarmStatus = 0;
            return;
        }

        $oCur = $myConfig->getActShopCurrencyObject();
        // convert currency to default
        $dPrice = $myUtils->currency2Float( $aParams['price'] );

        $oAlarm = oxNew( "oxpricealarm" );
        $oAlarm->oxpricealarm__oxuserid = new oxField( oxSession::getVar( 'usr' ));
        $oAlarm->oxpricealarm__oxemail  = new oxField( $aParams['email']);
        $oAlarm->oxpricealarm__oxartid  = new oxField( $aParams['aid']);
        $oAlarm->oxpricealarm__oxprice  = new oxField( $myUtils->fRound( $dPrice, $oCur ));
        $oAlarm->oxpricealarm__oxshopid = new oxField( $myConfig->getShopId());
        $oAlarm->oxpricealarm__oxcurrency = new oxField( $oCur->name);

        $oAlarm->oxpricealarm__oxlang = new oxField(oxRegistry::getLang()->getBaseLanguage());

        $oAlarm->save();

        // Send Email
        $oEmail = oxNew( 'oxemail' );
        $this->_iPriceAlarmStatus = (int) $oEmail->sendPricealarmNotification( $aParams, $oAlarm );
    }

    /**
     * Template variable getter. Returns bid price
     *
     * @return string
     */
    public function getBidPrice()
    {
        if ( $this->_sBidPrice === null ) {
            $this->_sBidPrice = false;

            $aParams = $this->_getParams();
            $oCur = $this->getConfig()->getActShopCurrencyObject();
            $iPrice = oxRegistry::getUtils()->currency2Float( $aParams['price'] );
            $this->_sBidPrice = oxRegistry::getLang()->formatCurrency( $iPrice, $oCur );
        }
        return $this->_sBidPrice;
    }

    /**
     * Template variable getter. Returns active article
     *
     * @return object
     */
    public function getProduct()
    {
        if ( $this->_oArticle === null ) {
            $this->_oArticle = false;
            $aParams = $this->_getParams();
            $oArticle = oxNew( 'oxarticle' );
            $oArticle->load( $aParams['aid'] );
            $this->_oArticle = $oArticle;
        }
        return $this->_oArticle;
    }

    /**
     * Returns params (article id, bid price)
     *
     * @return array
     */
    private function _getParams()
    {
        return oxConfig::getParameter( 'pa' );
    }

    /**
     * Return pricealarm status (if it was send)
     *
     * @return integer
     */
    public function getPriceAlarmStatus()
    {
        return $this->_iPriceAlarmStatus;
    }

}
