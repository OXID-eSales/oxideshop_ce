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

// defining login/logout states
define( 'USER_LOGIN_SUCCESS', 1 );
define( 'USER_LOGIN_FAIL', 2 );
define( 'USER_LOGOUT', 3 );

/**
 * User object manager.
 * Sets user details data, switches, logouts, logins user etc.
 * @subpackage oxcmp
 */
class oxcmp_user extends oxView
{
    /**
     * Boolean - if user is new or not.
     * @var bool
     */
    protected $_blIsNewUser    = false;

    /**
     * Marking object as component
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * Newsletter subscription status
     * @var bool
     */
    protected $_blNewsSubscriptionStatus = null;

    /**
     * User login state marker:
     *  - USER_LOGIN_SUCCESS - user successfully logged in;
     *  - USER_LOGIN_FAIL - login failed;
     *  - USER_LOGOUT - user logged out.
     * @var int
     */
    protected $_iLoginStatus = null;

    /**
     * Terms/conditions version number
     *
     * @var string
     */
    protected $_sTermsVer = null;

    /**
     * View classes accessible for not logged in customers
     *
     * @var array
     */
    protected $_aAllowedClasses = array(
                                        'register',
                                        'forgotpwd',
                                        'content',
                                        'account',
                                        'clearcookies',
                                        'oxwServiceMenu',
                                        );
    /**
     * Active login value
     *
     * @var bool
     */
    protected $_blActiveLogin = false;

    /**
     * Sets active login value
     *
     * @param boolean $blActiveLogin active login value
     *
     * @return null
     */
    protected function _setActiveLogin( $blActiveLogin )
    {
        $this->_blActiveLogin = $blActiveLogin;
    }

    /**
     * Returns active login value
     *
     * @return bool
     */
    protected function _getActiveLogin()
    {
        return $this->_blActiveLogin;
    }

    /**
     * Sets oxcmp_oxuser::blIsComponent = true, fetches user error
     * code and sets it to default - 0. Executes parent::init().
     *
     * Session variable:
     * <b>usr_err</b>
     *
     * @return null
     */
    public function init()
    {
        // saving show/hide delivery address state
        $blShow = oxConfig::getParameter( 'blshowshipaddress' );
        if (!isset($blShow)) {
            $blShow = oxSession::getVar( 'blshowshipaddress' );
        }

        oxSession::setVar( 'blshowshipaddress', $blShow );

        // load session user
        $this->_loadSessionUser();
        if ( $this->getConfig()->getConfigParam( 'blInvitationsEnabled' ) ) {
            // get invitor ID
            $this->getInvitor();
            $this->setRecipient();
        }

        parent::init();
    }

    /**
     * Executes parent::render(), oxcmp_user::_loadSessionUser(), loads user delivery
     * info. Returns user object oxcmp_user::oUser.
     *
     * Session variables:
     * <b>dgr</b>
     *
     * @return  object  user object
     */
    public function render()
    {
        // checks if private sales allows further tasks
        $this->_checkPsState();

        parent::render();

        // dyn_group feature: if you specify a groupid in URL the user
        // will automatically be added to this group later
        if ( $sDynGoup = oxConfig::getParameter( 'dgr' ) ) {
            oxSession::setVar( 'dgr', $sDynGoup );
        }

        return $this->getUser();
    }

    /**
     * If private sales enabled, checks:
     *  (1) if no session user and view can be accessed;
     *  (2) session user is available and accepted terms version matches actual version.
     * In case any condition is not satisfied redirects user to:
     *  (1) login page;
     *  (2) terms agreement page;
     *
     *  @return null
     */
    protected function _checkPsState()
    {
        $oConfig = $this->getConfig();
        if ( $this->getParent()->isEnabledPrivateSales() ) {
            // load session user
            $oUser  = $this->getUser();
            $sClass = $this->getParent()->getClassName();

            // no session user
            if ( !$oUser && !in_array( $sClass, $this->_aAllowedClasses ) ) {
                oxRegistry::getUtils()->redirect( $oConfig->getShopHomeURL() . 'cl=account', false, 302 );
            }

            if ( $oUser && !$oUser->isTermsAccepted() && !in_array( $sClass, $this->_aAllowedClasses ) ) {
                oxRegistry::getUtils()->redirect( $oConfig->getShopHomeURL() . 'cl=account&term=1', false, 302 );
            }
        }
    }

    /**
     * Tries to load user ID from session.
     *
     * @return null
     */
    protected function _loadSessionUser()
    {
        $myConfig = $this->getConfig();
        $oUser = $this->getUser();

        // no session user
        if ( !$oUser ) {
            return;
        }

        // this user is blocked, deny him
        if ( $oUser->inGroup( 'oxidblocked' ) ) {
            oxRegistry::getUtils()->redirect( $myConfig->getShopHomeURL() . 'cl=content&tpl=user_blocked.tpl', true, 302  );
        }

        // TODO: move this to a proper place
        if ( $oUser->isLoadedFromCookie() && !$myConfig->getConfigParam( 'blPerfNoBasketSaving' )) {

            if ( $oBasket = $this->getSession()->getBasket() ) {
                $oBasket->load();
                $oBasket->onUpdate();
            }
        }
    }

    /**
     * Collects posted user information from posted variables ("lgn_usr",
     * "lgn_pwd", "lgn_cook"), executes oxuser::login() and checks if
     * such user exists.
     *
     * Session variables:
     * <b>usr</b>, <b>usr_err</b>
     *
     * Template variables:
     * <b>usr_err</b>
     *
     * @return  string  redirection string
     */
    public function login()
    {
        $sUser     = oxConfig::getParameter( 'lgn_usr' );
        $sPassword = oxConfig::getParameter( 'lgn_pwd', true );
        $sCookie   = oxConfig::getParameter( 'lgn_cook' );
        //$blFbLogin = oxConfig::getParameter( 'fblogin' );

        $this->setLoginStatus( USER_LOGIN_FAIL );

        // trying to login user
        try {
            $oUser = oxNew( 'oxuser' );
            $oUser->login( $sUser, $sPassword, $sCookie );
            $this->setLoginStatus( USER_LOGIN_SUCCESS );
        } catch ( oxUserException $oEx ) {
            // for login component send excpetion text to a custom component (if defined)
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx, false, true, '', false );
            return 'user';
        } catch( oxCookieException $oEx ){
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
            return 'user';
        }
        // finalizing ..
        return $this->_afterLogin( $oUser );
    }

    /**
     * Special functionality which is performed after user logs in (or user is created without pass).
     * Performes additional checking if user is not BLOCKED (oxuser::InGroup("oxidblocked")) - if
     * yes - redirects to blocked user page ("cl=content&tpl=user_blocked.tpl"). If user status
     * is OK - sets user ID to session, automatically assigns him to dynamic
     * group (oxuser::addDynGroup(); if this directive is set (usually
     * by URL)). Stores cookie info if user confirmed in login screen.
     * Then loads delivery info and forces basket to recalculate
     * (oxsession::getBasket() + oBasket::blCalcNeeded = true). Returns
     * "payment" to redirect to payment screen. If problems occured loading
     * user - sets error code according problem, and returns "user" to redirect
     * to user info screen.
     *
     * @param oxuser $oUser user object
     *
     * @return string
     */
    protected function _afterLogin( $oUser )
    {
        $oSession = $this->getSession();

        // generating new session id after login
        if ( $this->getLoginStatus() === USER_LOGIN_SUCCESS ) {
            $oSession->regenerateSessionId();
        }

        $myConfig = $this->getConfig();

        // this user is blocked, deny him
        if ( $oUser->inGroup( 'oxidblocked' ) ) {
            oxRegistry::getUtils()->redirect( $myConfig->getShopHomeURL().'cl=content&tpl=user_blocked.tpl', true, 302 );
        }

        // adding to dyn group
        $oUser->addDynGroup(oxSession::getVar( 'dgr' ), $myConfig->getConfigParam( 'aDeniedDynGroups' ));

        // recalc basket
        if ( $oBasket = $oSession->getBasket() ) {
            $oBasket->onUpdate();
        }


        return 'payment';
    }

    /**
     * Executes oxcmp_user::login() method. After loggin user will not be
     * redirected to user or payment screens.
     *
     * @return null
     */
    public function login_noredirect()
    {
        $blAgb = oxConfig::getParameter( 'ord_agb' );
        $oConfig = $this->getConfig();
        if ( $this->getParent()->isEnabledPrivateSales() && $blAgb !== null && ( $oUser = $this->getUser() ) ) {
            if ( $blAgb ) {
                $oUser->acceptTerms();
            }
        } else {
            $this->login();

            if ( !$this->isAdmin() && !$this->getConfig()->getConfigParam( 'blPerfNoBasketSaving' )) {
                //load basket from the database
                try {
                    if ( $oBasket = $this->getSession()->getBasket() ) {
                        $oBasket->load();
                    }
                } catch ( Exception $oE ) {
                    //just ignore it
                }
            }


        }
    }

    /**
     * Executes oxcmp_user::login() and updates logged in user Facebook User ID (if user was
     * connected using Facebook Connect)
     *
     * @return null
     */
    public function login_updateFbId()
    {
        $this->login();

        if ( $oUser = $this->getUser() ) {
            //updating user Facebook ID
            if ( $oUser->updateFbId() ) {
                oxSession::setVar( '_blFbUserIdUpdated', true );
            }
        }
    }

    /**
     * Special utility function which is executed right after
     * oxcmp_user::logout is called. Currently it unsets such
     * session parameters as user chosen payment id, delivery
     * address id, active delivery set.
     *
     * @return null
     */
    protected function _afterLogout()
    {
        oxSession::deleteVar( 'paymentid' );
        oxSession::deleteVar( 'sShipSet' );
        oxSession::deleteVar( 'deladrid' );
        oxSession::deleteVar( 'dynvalue' );

        // resetting & recalc basket
        if ( ( $oBasket = $this->getSession()->getBasket() ) ) {
            $oBasket->resetUserInfo();
            $oBasket->onUpdate();
        }
    }

    /**
     * Deletes user information from session:<br>
     * "usr", "dgr", "dynvalue", "paymentid"<br>
     * also deletes cookie, unsets oxconfig::oUser,
     * oxcmp_user::oUser, forces basket to recalculate.
     *
     * @return null
     */
    public function logout()
    {
        $myConfig  = $this->getConfig();
        $oUser = oxNew( 'oxuser' );

        if ( $oUser->logout() ) {

            $this->setLoginStatus( USER_LOGOUT );

            // finalizing ..
            $this->_afterLogout();


            if ( $this->getParent()->isEnabledPrivateSales() ) {
                return 'account';
            }

            // redirecting if user logs out in SSL mode
            if ( oxConfig::getParameter('redirect') && $myConfig->getConfigParam( 'sSSLShopURL' ) ) {
                oxRegistry::getUtils()->redirect( $this->_getLogoutLink());
            }
        }
    }

    /**
     * Executes blUserRegistered = oxcmp_user::_changeUser_noRedirect().
     * if this returns true - returns "payment" (this redirects to
     * payment page), else returns blUserRegistered value.
     *
     * @see oxcmp_user::_changeUser_noRedirect()
     *
     * @return  mixed    redirection string or true if user is registered, false otherwise
     */
    public function changeUser( )
    {
        $blUserRegistered = $this->_changeUser_noRedirect();

        if ( $blUserRegistered === true ) {
            return 'payment';
        } else {
            return $blUserRegistered;
        }
    }

    /**
     * Executes oxcmp_user::_changeuser_noredirect().
     * returns "account_user" (this redirects to billing and shipping settings page) on success
     *
     * @return null
     */
    public function changeuser_testvalues()
    {
        // skip updating user info if this is just form reload
        // on selecting delivery address
        // We do redirect only on success not to loose errors.

        if ( $this->_changeUser_noRedirect() ) {
            return 'account_user';
        }
    }

    /**
     * First test if all MUST FILL fields were filled, then performed
     * additional checking oxcmp_user::CheckValues(). If no errors
     * occured - trying to create new user (oxuser::CreateUser()),
     * logging him to shop (oxuser::Login() if user has entered password)
     * or assigning him to dynamic group (oxuser::addDynGroup()).
     * If oxuser::CreateUser() returns false - thsi means user is
     * allready created - we only logging him to shop (oxcmp_user::Login()).
     * If there is any error with missing data - function will return
     * false and set error code (oxcmp_user::iError). If user was
     * created successfully - will return "payment" to redirect to
     * payment interface.
     *
     * Template variables:
     * <b>usr_err</b>
     *
     * Session variables:
     * <b>usr_err</b>, <b>usr</b>
     *
     * @return  mixed    redirection string or true if successful, false otherwise
     */
    public function createUser()
    {
        $blActiveLogin = $this->getParent()->isEnabledPrivateSales();
        $this->_setActiveLogin( $blActiveLogin );

        $myConfig = $this->getConfig();
        if ( $blActiveLogin && !oxConfig::getParameter( 'ord_agb' ) && $myConfig->getConfigParam( 'blConfirmAGB' ) ) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( 'READ_AND_CONFIRM_TERMS', false, true );
            return;
        }

        $myUtils  = oxRegistry::getUtils();

        // collecting values to check
        $sUser = oxConfig::getParameter( 'lgn_usr' );

        // first pass
        $sPassword = oxConfig::getParameter( 'lgn_pwd', true );

        // second pass
        $sPassword2 = oxConfig::getParameter( 'lgn_pwd2', true );

        $aInvAdress = oxConfig::getParameter( 'invadr', true );
        $aDelAdress = $this->_getDelAddressData();

        $oUser = oxNew( 'oxuser' );

        try {

            $oUser->checkValues( $sUser, $sPassword, $sPassword2, $aInvAdress, $aDelAdress );

            $iActState = $blActiveLogin ? 0 : 1;

            // setting values
            $oUser->oxuser__oxusername = new oxField($sUser, oxField::T_RAW);
            $oUser->setPassword( $sPassword );
            $oUser->oxuser__oxactive   = new oxField( $iActState, oxField::T_RAW);

            // used for checking if user email currently subscribed
            $iSubscriptionStatus = $oUser->getNewsSubscription()->getOptInStatus();

            $oUser->createUser();
            $oUser->load($oUser->getId());
            $oUser->changeUserData( $oUser->oxuser__oxusername->value, $sPassword, $sPassword, $aInvAdress, $aDelAdress );

            if ( $blActiveLogin ) {
                // accepting terms..
                $oUser->acceptTerms();
            }

            $sUserId = oxSession::getVar( "su" );
            $sRecEmail = oxSession::getVar( "re" );
            if ( $this->getConfig()->getConfigParam( 'blInvitationsEnabled' ) && $sUserId && $sRecEmail ) {
                // setting registration credit points..
                $oUser->setCreditPointsForRegistrant( $sUserId, $sRecEmail );
            }

            // assigning to newsletter
            $blOptin = oxRegistry::getConfig()->getRequestParameter( 'blnewssubscribed' );
            if ( $blOptin && $iSubscriptionStatus == 1 ) {
                // if user was assigned to newsletter and is creating account with newsletter checked, don't require confirm
                $oUser->getNewsSubscription()->setOptInStatus(1);
                $oUser->addToGroup( 'oxidnewsletter' );
                $this->_blNewsSubscriptionStatus = 1;
            } else {
                $this->_blNewsSubscriptionStatus = $oUser->setNewsSubscription( $blOptin, $this->getConfig()->getConfigParam( 'blOrderOptInEmail' ) );
            }

            $oUser->addToGroup( 'oxidnotyetordered' );
            $oUser->addDynGroup( oxSession::getVar( 'dgr' ), $myConfig->getConfigParam( 'aDeniedDynGroups' ) );
            $oUser->logout();

        } catch ( oxUserException $oEx ) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx, false, true );
            return false;
        } catch( oxInputException $oEx ){
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx, false, true );
            return false;
        } catch( oxConnectionException $oEx ){
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx, false, true );
            return false;
        }

        $this->_setOrderRemark( $oUser );

        // send register eMail
        //TODO: move into user
        $this->_sendRegistrationEmail( $oUser );

        // new registered
        $this->_blIsNewUser = true;

        return 'payment';
    }

    /**
     * Creates new oxid user
     *
     * @return string partial parameter string or null
     */
    public function registerUser()
    {
        // registered new user ?
        if ( $this->createuser()!= false && $this->_blIsNewUser ) {
            if ( $this->_blNewsSubscriptionStatus === null || $this->_blNewsSubscriptionStatus ) {
                return 'register?success=1';
            } else {
                return 'register?success=1&newslettererror=4';
            }
        } else {
            // problems with registration ...
            $this->logout();
        }
    }

    /**
     * Mostly used for customer profile editing screen (OXID eShop ->
     * MY ACCOUNT). Checks if oUser is set (oxcmp_user::oUser) - if
     * not - executes oxcmp_user::_loadSessionUser(). If user unchecked newsletter
     * subscription option - removes him from this group. There is an
     * additional MUST FILL fields checking. Function returns true or false
     * according to user data submission status.
     *
     * Session variables:
     * <b>ordrem</b>
     *
     * @return  bool true on success, false otherwise
     */
    protected function _changeUser_noRedirect( )
    {
        if (!$this->getSession()->checkSessionChallenge()) {
            return;
        }

        // no user ?
        $oUser = $this->getUser();
        if ( !$oUser ) {
            return;
        }

        // collecting values to check
        $aDelAdress = $this->_getDelAddressData();

        // if user company name, user name and additional info has special chars
        $aInvAdress = oxConfig::getParameter( 'invadr', true );

        $sUserName  = $oUser->oxuser__oxusername->value;
        $sPassword  = $sPassword2 = $oUser->oxuser__oxpassword->value;

        try { // testing user input
            $oUser->changeUserData( $sUserName, $sPassword, $sPassword2, $aInvAdress, $aDelAdress );
            // assigning to newsletter
            if (($blOptin = oxConfig::getParameter( 'blnewssubscribed' )) === null) {
                $blOptin = $oUser->getNewsSubscription()->getOptInStatus();
            }
            // check if email address changed, if so, force check news subscription settings.
            $blForceCheckOptIn = ( $aInvAdress['oxuser__oxusername'] !== null && $aInvAdress['oxuser__oxusername'] !== $sUserName );
            $this->_blNewsSubscriptionStatus = $oUser->setNewsSubscription( $blOptin, $this->getConfig()->getConfigParam( 'blOrderOptInEmail' ), $blForceCheckOptIn );

        } catch ( oxUserException $oEx ) { // errors in input
            // marking error code
            //TODO
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($oEx, false, true);
            return;
        } catch(oxInputException $oEx) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($oEx, false, true);
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($oEx, false, true, 'input_not_all_fields');
            return;
        } catch(oxConnectionException $oEx){
             //connection to external resource broken, change message and pass to the view
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($oEx, false, true);
            return;
        }


        // order remark
        $sOrderRemark = oxConfig::getParameter( 'order_remark', true );

        if ( $sOrderRemark ) {
            oxSession::setVar( 'ordrem', $sOrderRemark );
        } else {
            oxSession::deleteVar( 'ordrem' );
        }

        if ( $oBasket = $this->getSession()->getBasket() ) {
            $oBasket->onUpdate();
        }
        return true;
    }

    /**
     * Returns delivery address from request. Before returning array is checked if
     * all needed data is there
     *
     * @return array
     */
    protected function _getDelAddressData()
    {
        // if user company name, user name and additional info has special chars
        $aDelAdress = $aDeladr = (oxConfig::getParameter( 'blshowshipaddress' ) || oxSession::getVar( 'blshowshipaddress' )) ? oxConfig::getParameter( 'deladr', true ) : array();

        if ( is_array( $aDeladr ) ) {
            // checking if data is filled
            if ( isset( $aDeladr['oxaddress__oxsal'] ) ) {
                unset( $aDeladr['oxaddress__oxsal'] );
            }
            if ( !count( $aDeladr ) || implode( '', $aDeladr ) == '' ) {
                // resetting to avoid empty records
                $aDelAdress = array();
            }
        }
        return $aDelAdress;
    }

    /**
     * Returns logout link with additional params
     *
     * @return string $sLogoutLink
     */
    protected function _getLogoutLink()
    {
        $oConfig = $this->getConfig();

        $sLogoutLink = $oConfig->isSsl()? $oConfig->getShopSecureHomeUrl() : $oConfig->getShopHomeUrl();
        $sLogoutLink .= 'cl='.$oConfig->getRequestParameter('cl').$this->getParent()->getDynUrlParams();
        if ( $sParam = $oConfig->getRequestParameter('anid') ) {
            $sLogoutLink .= '&amp;anid='.$sParam;
        }
        if ( $sParam = $oConfig->getRequestParameter('cnid') ) {
            $sLogoutLink .= '&amp;cnid='.$sParam;
        }
        if ( $sParam = $oConfig->getRequestParameter('mnid') ) {
            $sLogoutLink .= '&amp;mnid='.$sParam;
        }
        if ( $sParam = $oConfig->getRequestParameter('tpl') ) {
            $sLogoutLink .= '&amp;tpl='.$sParam;
        }
        if ( $sParam = $oConfig->getRequestParameter('oxloadid') ) {
            $sLogoutLink .= '&amp;oxloadid='.$sParam;
        }
        if ( $sParam = $oConfig->getRequestParameter('recommid') ) {
            $sLogoutLink .= '&amp;recommid='.$sParam;
        }
        return $sLogoutLink.'&amp;fnc=logout';
    }

    /**
     * Sets user login state
     *
     * @param int $iStatus login state (USER_LOGIN_SUCCESS/USER_LOGIN_FAIL/USER_LOGOUT)
     *
     * @return null
     */
    public function setLoginStatus( $iStatus )
    {
        $this->_iLoginStatus = $iStatus;
    }

    /**
     * Returns user login state marker:
     *  - USER_LOGIN_SUCCESS - user successfully logged in;
     *  - USER_LOGIN_FAIL - login failed;
     *  - USER_LOGOUT - user logged out.
     *
     * @return int
     */
    public function getLoginStatus()
    {
        return $this->_iLoginStatus;
    }

    /**
     * Gets from URL invitor id
     *
     * @return null
     */
    public function getInvitor()
    {
        $sSu = oxSession::getVar( 'su' );
        if ( !$sSu && ( $sSuNew = oxConfig::getParameter( 'su' ) ) ) {
            oxSession::setVar( 'su', $sSuNew );
        }
    }

    /**
     * sets from URL invitor id
     *
     * @return null
     */
    public function setRecipient()
    {
        $sRe = oxSession::getVar( 're' );
        if ( !$sRe && ( $sReNew = oxConfig::getParameter( 're' ) ) ) {
            oxSession::setVar( 're', $sReNew );
        }
    }

    /**
     * Sets order remark session variable if active login is true
     *
     * @param object $oUser user object
     *
     * @return null
     */
    public function _setOrderRemark( $oUser )
    {
        $blActiveLogin = $this->_getActiveLogin();
        if ( !$blActiveLogin ) {

            oxRegistry::getSession()->setVariable( 'usr', $oUser->getId() );
            $this->_afterLogin( $oUser );


            // order remark
            //V #427: order remark for new users
            $sOrderRemark = oxRegistry::getConfig()->getRequestParameter( 'order_remark', true );
            if ( $sOrderRemark ) {
                oxRegistry::getSession()->setVariable( 'ordrem', $sOrderRemark );
            }
        }
    }

    /**
     * Sends registration email if option parameter is set to 3
     *
     * @param object $oUser user object
     *
     * @return false
     */
    public function _sendRegistrationEmail( $oUser )
    {
        $blActiveLogin = $this->_getActiveLogin();
        if ( (int) oxRegistry::getConfig()->getRequestParameter( 'option' ) == 3 ) {
            $oxEMail = oxNew( 'oxemail' );
            if ( $blActiveLogin ) {
                $oxEMail->sendRegisterConfirmEmail( $oUser );
            } else {
                $oxEMail->sendRegisterEmail( $oUser );
            }
        }
    }
}
