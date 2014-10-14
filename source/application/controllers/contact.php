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
 * Contact window.
 * Arranges "CONTACT" window, by creating form for user opinion (etc.)
 * submission. After user correctly
 * fulfils all required fields all information is sent to shop owner by
 * email. OXID eShop -> CONTACT.
 */
class Contact extends oxUBase
{
    /**
     * Entered user data.
     * @var array
     */
    protected $_aUserData = null;

    /**
     * Entered contact subject.
     * @var string
     */
    protected $_sContactSubject = null;

    /**
     * Entered conatct message.
     * @var string
     */
    protected $_sContactMessage = null;

    /**
     * Class handling CAPTCHA image.
     * @var object
     */
    protected $_oCaptcha = null;

    /**
     * Contact email send status.
     * @var object
     */
    protected $_blContactSendStatus = null;

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'page/info/contact.tpl';

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Composes and sends user written message, returns false if some parameters
     * are missing.
     *
     * @return bool
     */
    public function send()
    {
        $aParams = oxConfig::getParameter( 'editval' );

        // checking email address
        if ( !oxRegistry::getUtils()->isValidEmail( $aParams['oxuser__oxusername'] ) ) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( 'ERROR_MESSAGE_INPUT_NOVALIDEMAIL' );
            return false;
        }

        // spam spider prevension
        $sMac     = oxConfig::getParameter( 'c_mac' );
        $sMacHash = oxConfig::getParameter( 'c_mach' );
        $oCaptcha = $this->getCaptcha();

        if ( !$oCaptcha->pass( $sMac, $sMacHash ) ) {
            // even if there is no exception, use this as a default display method
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( 'MESSAGE_WRONG_VERIFICATION_CODE' );
            return false;
        }

        $sSubject = oxConfig::getParameter( 'c_subject' );
        if ( !$aParams['oxuser__oxfname'] || !$aParams['oxuser__oxlname'] || !$aParams['oxuser__oxusername'] || !$sSubject ) {
            // even if there is no exception, use this as a default display method
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( 'ERROR_MESSAGE_INPUT_NOTALLFIELDS' );
            return false;
        }

        $oLang = oxRegistry::getLang();
        $sMessage  = $oLang->translateString( 'MESSAGE_FROM' ) . " " .
                     $oLang->translateString( $aParams['oxuser__oxsal'] ) ." " .
                     $aParams['oxuser__oxfname'] . " " .
                     $aParams['oxuser__oxlname'] . "(" .$aParams['oxuser__oxusername'] . ")<br /><br />" .
                     nl2br( oxConfig::getParameter( 'c_message' ) );

        $oEmail = oxNew( 'oxemail' );
        if ( $oEmail->sendContactMail( $aParams['oxuser__oxusername'], $sSubject, $sMessage ) ) {
            $this->_blContactSendStatus = 1;
        } else {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay('ERROR_MESSAGE_CHECK_EMAIL');
        }
    }

    /**
     * Template variable getter. Returns entered user data
     *
     * @return object
     */
    public function getUserData()
    {
        if ( $this->_oUserData === null ) {
            $this->_oUserData = oxConfig::getParameter( 'editval' );
        }
        return $this->_oUserData;
    }

    /**
     * Template variable getter. Returns entered contact subject
     *
     * @return object
     */
    public function getContactSubject()
    {
        if ( $this->_sContactSubject === null ) {
            $this->_sContactSubject = oxConfig::getParameter( 'c_subject' );
        }
        return $this->_sContactSubject;
    }

    /**
     * Template variable getter. Returns entered message
     *
     * @return object
     */
    public function getContactMessage()
    {
        if ( $this->_sContactMessage === null ) {
            $this->_sContactMessage = oxConfig::getParameter( 'c_message' );
        }
        return $this->_sContactMessage;
    }

    /**
     * Template variable getter. Returns object of handling CAPTCHA image
     *
     * @return object
     */
    public function getCaptcha()
    {
        if ( $this->_oCaptcha === null ) {
            $this->_oCaptcha = oxNew('oxCaptcha');
        }
        return $this->_oCaptcha;
    }

    /**
     * Template variable getter. Returns status if email was send succesfull
     *
     * @return object
     */
    public function getContactSendStatus()
    {
        return $this->_blContactSendStatus;
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

        $aPath['title'] = oxRegistry::getLang()->translateString( 'CONTACT', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
