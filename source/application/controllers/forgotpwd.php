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
 * Password reminder page.
 * Collects toparticle, bargain article list. There is a form with entry
 * field to enter login name (usually email). After user enters required
 * information and submits "Request Password" button mail is sent to users email.
 * OXID eShop -> MY ACCOUNT -> "Forgot your password? - click here."
 */
class ForgotPwd extends oxUBase
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'page/account/forgotpwd.tpl';

    /**
     * Send forgot E-Mail.
     * @var string
     */
    protected $_sForgotEmail = null;

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Update link expiration status
     *
     * @var bool
     */
    protected $_blUpdateLinkStatus = null;

    /**
     * Sign if to load and show bargain action
     * @var bool
     */
    protected $_blBargainAction = true;

    /**
     * Executes oxemail::SendForgotPwdEmail() and sends login
     * password to user according to login name (email).
     *
     * Template variables:
     * <b>sendForgotMail</b>
     *
     * @return null
     */
    public function forgotPassword()
    {
        $sEmail = oxConfig::getParameter( 'lgn_usr' );
        $this->_sForgotEmail = $sEmail;
        $oEmail = oxNew( 'oxemail' );

        // problems sending passwd reminder ?
        $iSuccess = false;
        if ( $sEmail ) {
            $iSuccess = $oEmail->sendForgotPwdEmail( $sEmail );
        }
        if ( $iSuccess !== true ) {
            $sError = ($iSuccess === false)? 'ERROR_MESSAGE_PASSWORD_EMAIL_INVALID' : 'MESSAGE_NOT_ABLE_TO_SEND_EMAIL';
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($sError, false, true);
            $this->_sForgotEmail = false;
        }
    }

    /**
     * Checks if password is fine and updates old one with new
     * password. On success user is redirected to success page
     *
     * @return string
     */
    public function updatePassword()
    {
        $sNewPass  = oxConfig::getParameter( 'password_new', true );
        $sConfPass = oxConfig::getParameter( 'password_new_confirm', true );

        $oUser = oxNew( 'oxuser' );
        if ( ( $oExcp = $oUser->checkPassword( $sNewPass, $sConfPass, true ) ) ) {
            switch ( $oExcp->getMessage() ) {
                case 'ERROR_MESSAGE_INPUT_EMPTYPASS':
                case 'ERROR_MESSAGE_PASSWORD_TOO_SHORT':
                    return oxRegistry::get("oxUtilsView")->addErrorToDisplay('ERROR_MESSAGE_PASSWORD_TOO_SHORT', false, true);
                default:
                    return oxRegistry::get("oxUtilsView")->addErrorToDisplay('ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH', false, true);
            }
        }

        // passwords are fine - updating and loggin user in
        if ( $oUser->loadUserByUpdateId( $this->getUpdateId() ) ) {

            // setting new pass ..
            $oUser->setPassword( $sNewPass );

            // resetting update pass params
            $oUser->setUpdateKey( true );

            // saving ..
            $oUser->save();

            // forcing user login
            oxSession::setVar( 'usr', $oUser->getId() );
            return 'forgotpwd?success=1';
        } else {
            // expired reminder
            return oxRegistry::get("oxUtilsView")->addErrorToDisplay( 'ERROR_MESSAGE_PASSWORD_LINK_EXPIRED', false, true );
        }
    }

    /**
     * If user password update was successfull - setting success status
     *
     * @return bool
     */
    public function updateSuccess()
    {
        return (bool) oxConfig::getParameter( 'success' );
    }

    /**
     * Notifies that password update form must be shown
     *
     * @return bool
     */
    public function showUpdateScreen()
    {
        return (bool) $this->getUpdateId();
    }

    /**
     * Returns special id used for password update functionality
     *
     * @return string
     */
    public function getUpdateId()
    {
        return oxConfig::getParameter( 'uid' );
    }

    /**
     * Returns password update link expiration status
     *
     * @return bool
     */
    public function isExpiredLink()
    {
        if ( ( $sKey = $this->getUpdateId() ) ) {
            $blExpired = oxNew( 'oxuser' )->isExpiredUpdateId( $sKey );
        }

        return $blExpired;
    }

    /**
     * Template variable getter. Returns searched article list
     *
     * @return string
     */
    public function getForgotEmail()
    {
        return $this->_sForgotEmail;
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

        $aPath['title'] = oxRegistry::getLang()->translateString( 'FORGOT_PASSWORD', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
