<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component;

use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Form\FormFields;
use OxidEsales\Eshop\Core\Form\FormFieldsTrimmer;
use OxidEsales\Eshop\Core\Form\UpdatableFieldsConstructor;
use OxidEsales\Eshop\Core\Request;
use Exception;
use OxidEsales\Eshop\Core\Contract\AbstractUpdatableFields;
use OxidEsales\Eshop\Application\Model\User\UserUpdatableFields;
use OxidEsales\Eshop\Application\Model\User\UserShippingAddressUpdatableFields;
use OxidEsales\EshopCommunity\Application\Model\User;

// defining login/logout states
define('USER_LOGIN_SUCCESS', 1);
define('USER_LOGIN_FAIL', 2);
define('USER_LOGOUT', 3);

/**
 * User object manager.
 * Sets user details data, switches, logouts, logins user etc.
 *
 * @subpackage oxcmp
 */
class UserComponent extends \OxidEsales\Eshop\Core\Controller\BaseController
{
    /**
     * Boolean - if user is new or not.
     *
     * @var bool
     */
    protected $_blIsNewUser = false;

    /**
     * Marking object as component
     *
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * Newsletter subscription status
     *
     * @var bool
     */
    protected $_blNewsSubscriptionStatus = null;

    /**
     * User login state marker:
     *  - USER_LOGIN_SUCCESS - user successfully logged in;
     *  - USER_LOGIN_FAIL - login failed;
     *  - USER_LOGOUT - user logged out.
     *
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
    protected $_aAllowedClasses = [
        'register',
        'forgotpwd',
        'content',
        'account',
        'clearcookies',
        'oxwservicemenu',
        'oxwminibasket',
    ];

    /**
     * Sets oxcmp_oxuser::blIsComponent = true, fetches user error
     * code and sets it to default - 0. Executes parent::init().
     *
     * Session variable:
     * <b>usr_err</b>
     */
    public function init()
    {
        $this->_saveDeliveryAddressState();
        $this->_loadSessionUser();
        $this->_saveInvitor();

        parent::init();
    }

    /**
     * Executes parent::render(), oxcmp_user::_loadSessionUser(), loads user delivery
     * info. Returns user object oxcmp_user::oUser.
     *
     * @return  object  user object
     */
    public function render()
    {
        // checks if private sales allows further tasks
        $this->_checkPsState();

        parent::render();

        return $this->getUser();
    }

    /**
     * If private sales enabled, checks:
     *  (1) if no session user and view can be accessed;
     *  (2) session user is available and accepted terms version matches actual version.
     * In case any condition is not satisfied redirects user to:
     *  (1) login page;
     *  (2) terms agreement page;
     */
    protected function _checkPsState()
    {
        $oConfig = $this->getConfig();
        if ($this->getParent()->isEnabledPrivateSales()) {
            // load session user
            $oUser = $this->getUser();
            $sClass = $this->getParent()->getClassName();

            // no session user
            if (!$oUser && !in_array($sClass, $this->_aAllowedClasses)) {
                Registry::getUtils()->redirect($oConfig->getShopHomeUrl() . 'cl=account', false, 302);
            }

            if ($oUser && !$oUser->isTermsAccepted() && !in_array($sClass, $this->_aAllowedClasses)) {
                Registry::getUtils()->redirect($oConfig->getShopHomeUrl() . 'cl=account&term=1', false, 302);
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
        if (!$oUser) {
            return;
        }

        // this user is blocked, deny him
        if ($oUser->inGroup('oxidblocked')) {
            $sUrl = $myConfig->getShopHomeUrl() . 'cl=content&tpl=user_blocked.tpl';
            Registry::getUtils()->redirect($sUrl, true, 302);
        }

        // TODO: move this to a proper place
        if ($oUser->isLoadedFromCookie() && !$myConfig->getConfigParam('blPerfNoBasketSaving')) {
            if ($oBasket = $this->getSession()->getBasket()) {
                $oBasket->load();
                $oBasket->onUpdate();
            }
        }
    }

    /**
     * Collects posted user information from posted variables ("lgn_usr",
     * "lgn_pwd", "lgn_cook"), executes \OxidEsales\Eshop\Application\Model\User::login() and checks if
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
        $sUser = Registry::getConfig()->getRequestParameter('lgn_usr');
        $sPassword = Registry::getConfig()->getRequestParameter('lgn_pwd', true);
        $sCookie = Registry::getConfig()->getRequestParameter('lgn_cook');

        $this->setLoginStatus(USER_LOGIN_FAIL);

        // trying to login user
        try {
            /** @var \OxidEsales\Eshop\Application\Model\User $oUser */
            $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
            $oUser->login($sUser, $sPassword, $sCookie);
            $this->setLoginStatus(USER_LOGIN_SUCCESS);
        } catch (\OxidEsales\Eshop\Core\Exception\UserException $oEx) {
            // for login component send exception text to a custom component (if defined)
            Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, '', false);

            return 'user';
        } catch (\OxidEsales\Eshop\Core\Exception\CookieException $oEx) {
            Registry::getUtilsView()->addErrorToDisplay($oEx);

            return 'user';
        }

        // finalizing ..
        return $this->_afterLogin($oUser);
    }

    /**
     * Special functionality which is performed after user logs in (or user is created without pass).
     * Performes additional checking if user is not BLOCKED
     * (\OxidEsales\Eshop\Application\Model\User::InGroup("oxidblocked")) - if yes - redirects to blocked user
     * page ("cl=content&tpl=user_blocked.tpl").
     * Stores cookie info if user confirmed in login screen.
     * Then loads delivery info and forces basket to recalculate
     * (\OxidEsales\Eshop\Core\Session::getBasket() + oBasket::blCalcNeeded = true). Returns
     * "payment" to redirect to payment screen. If problems occured loading
     * user - sets error code according problem, and returns "user" to redirect
     * to user info screen.
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser user object
     *
     * @return string
     */
    protected function _afterLogin($oUser)
    {
        $oSession = $this->getSession();

        // generating new session id after login
        if ($this->getLoginStatus() === USER_LOGIN_SUCCESS) {
            $oSession->regenerateSessionId();
        }

        $myConfig = $this->getConfig();

        // this user is blocked, deny him
        if ($oUser->inGroup('oxidblocked')) {
            $sUrl = $myConfig->getShopHomeUrl() . 'cl=content&tpl=user_blocked.tpl';
            Registry::getUtils()->redirect($sUrl, true, 302);
        }

        // recalc basket
        if ($oBasket = $oSession->getBasket()) {
            $oBasket->onUpdate();
        }

        return 'payment';
    }

    /**
     * Executes oxcmp_user::login() method. After loggin user will not be
     * redirected to user or payment screens.
     */
    public function login_noredirect()
    {
        $blAgb = Registry::getConfig()->getRequestParameter('ord_agb');

        if ($this->getParent()->isEnabledPrivateSales() && $blAgb !== null && ($oUser = $this->getUser())) {
            if ($blAgb) {
                $oUser->acceptTerms();
            }
        } else {
            $this->login();

            if (!$this->isAdmin() && !$this->getConfig()->getConfigParam('blPerfNoBasketSaving')) {
                //load basket from the database
                try {
                    if ($oBasket = $this->getSession()->getBasket()) {
                        $oBasket->load();
                    }
                } catch (Exception $oE) {
                    //just ignore it
                }
            }
        }
    }

    /**
     * Special utility function which is executed right after
     * oxcmp_user::logout is called. Currently it unsets such
     * session parameters as user chosen payment id, delivery
     * address id, active delivery set.
     */
    protected function _afterLogout()
    {
        Registry::getSession()->deleteVariable('paymentid');
        Registry::getSession()->deleteVariable('sShipSet');
        Registry::getSession()->deleteVariable('deladrid');
        Registry::getSession()->deleteVariable('dynvalue');

        // resetting & recalc basket
        if (($oBasket = $this->getSession()->getBasket())) {
            $oBasket->resetUserInfo();
            $oBasket->onUpdate();
        }

        Registry::getSession()->delBasket();
    }

    /**
     * Deletes user information from session:<br>
     * "usr", "dynvalue", "paymentid"<br>
     * also deletes cookie, unsets \OxidEsales\Eshop\Core\Config::oUser,
     * oxcmp_user::oUser, forces basket to recalculate.
     *
     * @return null
     */
    public function logout()
    {
        $myConfig = $this->getConfig();
        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);

        if ($oUser->logout()) {
            $this->setLoginStatus(USER_LOGOUT);

            // finalizing ..
            $this->_afterLogout();

            $this->resetPermissions();

            if ($this->getParent()->isEnabledPrivateSales()) {
                return 'account';
            }

            // redirecting if user logs out in SSL mode
            if (Registry::getConfig()->getRequestParameter('redirect') && $myConfig->getConfigParam('sSSLShopURL')) {
                Registry::getUtils()->redirect($this->_getLogoutLink());
            }
        }
    }

    /**
     * Any additional permission reset actions required on logout or changeuser actions
     */
    protected function resetPermissions()
    {
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
    public function changeUser()
    {
        return ($this->_changeUser_noRedirect() === true) ? 'payment' : false;
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

        if ($this->_changeUser_noRedirect()) {
            return 'account_user';
        }
    }

    /**
     * First test if all required fields were filled, then performed
     * additional checking oxcmp_user::CheckValues(). If no errors
     * occured - trying to create new user (\OxidEsales\Eshop\Application\Model\User::CreateUser()),
     * logging him to shop (\OxidEsales\Eshop\Application\Model\User::Login() if user has entered password).
     * If \OxidEsales\Eshop\Application\Model\User::CreateUser() returns false - this means user is
     * already created - we only logging him to shop (oxcmp_user::Login()).
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

        $oConfig = $this->getConfig();

        if ($blActiveLogin && !$oConfig->getRequestParameter('ord_agb') && $oConfig->getConfigParam('blConfirmAGB')) {
            Registry::getUtilsView()->addErrorToDisplay('READ_AND_CONFIRM_TERMS', false, true);

            return false;
        }

        // collecting values to check
        $sUser = $oConfig->getRequestParameter('lgn_usr');

        // first pass
        $sPassword = $oConfig->getRequestParameter('lgn_pwd', true);

        // second pass
        $sPassword2 = $oConfig->getRequestParameter('lgn_pwd2', true);

        $aInvAdress = $oConfig->getRequestParameter('invadr', true);

        $aInvAdress = $this->cleanAddress($aInvAdress, oxNew(UserUpdatableFields::class));
        $aInvAdress = $this->trimAddress($aInvAdress);

        $aDelAdress = $this->_getDelAddressData();
        $aDelAdress = $this->cleanAddress($aDelAdress, oxNew(UserShippingAddressUpdatableFields::class));
        $aDelAdress = $this->trimAddress($aDelAdress);

        try {
            /** @var \OxidEsales\Eshop\Application\Model\User $oUser */
            $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
            $oUser->checkValues($sUser, $sPassword, $sPassword2, $aInvAdress, $aDelAdress);

            $iActState = $blActiveLogin ? 0 : 1;

            // setting values
            $oUser->oxuser__oxusername = new Field($sUser, Field::T_RAW);
            $oUser->setPassword($sPassword);
            $oUser->oxuser__oxactive = new Field($iActState, Field::T_RAW);

            // used for checking if user email currently subscribed
            $iSubscriptionStatus = $oUser->getNewsSubscription()->getOptInStatus();

            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $database->startTransaction();
            try {
                $oUser->createUser();
                $oUser = $this->configureUserBeforeCreation($oUser);
                $oUser->load($oUser->getId());
                $oUser->changeUserData(
                    $oUser->oxuser__oxusername->value,
                    $sPassword,
                    $sPassword,
                    $aInvAdress,
                    $aDelAdress
                );

                if ($blActiveLogin) {
                    // accepting terms..
                    $oUser->acceptTerms();
                }

                $database->commitTransaction();
            } catch (Exception $exception) {
                $database->rollbackTransaction();

                throw $exception;
            }

            $sUserId = Registry::getSession()->getVariable("su");
            $sRecEmail = Registry::getSession()->getVariable("re");
            if ($this->getConfig()->getConfigParam('blInvitationsEnabled') && $sUserId && $sRecEmail) {
                // setting registration credit points..
                $oUser->setCreditPointsForRegistrant($sUserId, $sRecEmail);
            }

            // assigning to newsletter
            $blOptin = Registry::getConfig()->getRequestParameter('blnewssubscribed');
            if ($blOptin && $iSubscriptionStatus == 1) {
                // if user was assigned to newsletter
                // and is creating account with newsletter checked,
                // don't require confirm
                $oUser->getNewsSubscription()->setOptInStatus(1);
                $oUser->addToGroup('oxidnewsletter');
                $this->_blNewsSubscriptionStatus = 1;
            } else {
                $blOrderOptInEmailParam = $this->getConfig()->getConfigParam('blOrderOptInEmail');
                $this->_blNewsSubscriptionStatus = $oUser->setNewsSubscription($blOptin, $blOrderOptInEmailParam);
            }

            $oUser->addToGroup('oxidnotyetordered');
            $oUser->logout();
        } catch (\OxidEsales\Eshop\Core\Exception\UserException $exception) {
            Registry::getUtilsView()->addErrorToDisplay($exception, false, true);

            return false;
        } catch (\OxidEsales\Eshop\Core\Exception\InputException $exception) {
            Registry::getUtilsView()->addErrorToDisplay($exception, false, true);

            return false;
        } catch (\OxidEsales\Eshop\Core\Exception\DatabaseConnectionException $exception) {
            Registry::getUtilsView()->addErrorToDisplay($exception, false, true);

            return false;
        } catch (\OxidEsales\Eshop\Core\Exception\ConnectionException $exception) {
            Registry::getUtilsView()->addErrorToDisplay($exception, false, true);

            return false;
        }

        if (!$blActiveLogin) {
            Registry::getSession()->setVariable('usr', $oUser->getId());
            $this->_afterLogin($oUser);

            // order remark
            //V #427: order remark for new users
            $sOrderRemark = Registry::getConfig()->getRequestParameter('order_remark', true);
            if ($sOrderRemark) {
                Registry::getSession()->setVariable('ordrem', $sOrderRemark);
            }
        }

        // send register eMail
        //TODO: move into user
        if ((int) Registry::getConfig()->getRequestParameter('option') == 3) {
            $oxEMail = oxNew(\OxidEsales\Eshop\Core\Email::class);
            if ($blActiveLogin) {
                $oxEMail->sendRegisterConfirmEmail($oUser);
            } else {
                $oxEMail->sendRegisterEmail($oUser);
            }
        }

        // new registered
        $this->_blIsNewUser = true;

        $sAction = 'payment?new_user=1&success=1';
        if ($this->_blNewsSubscriptionStatus !== null && !$this->_blNewsSubscriptionStatus) {
            $sAction = 'payment?new_user=1&success=1&newslettererror=4';
        }

        return $sAction;
    }

    /**
     * If any additional configurations required right before user creation
     *
     * @param \OxidEsales\Eshop\Application\Model\User $user
     *
     * @return \OxidEsales\Eshop\Application\Model\User The user we gave in.
     */
    protected function configureUserBeforeCreation($user)
    {
        return $user;
    }

    /**
     * Creates new oxid user
     *
     * @return string partial parameter string or null
     */
    public function registerUser()
    {
        // registered new user ?
        if ($this->createUser() != false && $this->_blIsNewUser) {
            if ($this->_blNewsSubscriptionStatus === null || $this->_blNewsSubscriptionStatus) {
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
     * Deletes user shipping address.
     */
    public function deleteShippingAddress()
    {
        $request = oxNew(Request::class);
        $addressId = $request->getRequestParameter('oxaddressid');

        $address = oxNew(Address::class);
        $address->load($addressId);
        if ($this->canUserDeleteShippingAddress($address) && $this->getSession()->checkSessionChallenge()) {
            $address->delete($addressId);
        }
    }

    /**
     * Checks if shipping address is assigned to user.
     *
     * @param Address $address
     * @return bool
     */
    private function canUserDeleteShippingAddress($address)
    {
        $canDelete = false;
        $user = $this->getUser();
        if ($address->oxaddress__oxuserid->value === $user->getId()) {
            $canDelete = true;
        }

        return $canDelete;
    }

    /**
     * Saves invitor ID
     */
    protected function _saveInvitor()
    {
        if ($this->getConfig()->getConfigParam('blInvitationsEnabled')) {
            $this->getInvitor();
            $this->setRecipient();
        }
    }

    /**
     * Saving show/hide delivery address state
     */
    protected function _saveDeliveryAddressState()
    {
        $oSession = Registry::getSession();

        $blShow = Registry::getConfig()->getRequestParameter('blshowshipaddress');
        if (!isset($blShow)) {
            $blShow = $oSession->getVariable('blshowshipaddress');
        }

        $oSession->setVariable('blshowshipaddress', $blShow);
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
     * @deprecated since v6.0.0 (2017-02-27); Use changeUserWithoutRedirect().
     *
     * @return  bool true on success, false otherwise
     */
    protected function _changeUser_noRedirect()
    {
        return $this->changeUserWithoutRedirect();
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
    protected function changeUserWithoutRedirect()
    {
        if (!$this->getSession()->checkSessionChallenge()) {
            return;
        }

        // no user ?
        $oUser = $this->getUser();
        if (!$oUser) {
            return;
        }

        // collecting values to check
        $aDelAdress = $this->_getDelAddressData();
        $aDelAdress = $this->cleanAddress($aDelAdress, oxNew(UserShippingAddressUpdatableFields::class));
        $aDelAdress = $this->trimAddress($aDelAdress);

        // if user company name, user name and additional info has special chars
        $aInvAdress = Registry::getConfig()->getRequestParameter('invadr', true);
        $aInvAdress = $this->cleanAddress($aInvAdress, oxNew(UserUpdatableFields::class));
        $aInvAdress = $this->trimAddress($aInvAdress);

        $sUserName = $oUser->oxuser__oxusername->value;
        $sPassword = $sPassword2 = $oUser->oxuser__oxpassword->value;

        try { // testing user input
            // delete user if it is a guest user
            if (isset($aInvAdress['oxuser__oxusername'])) {
                if (!$this->deleteGuestUser($aInvAdress['oxuser__oxusername'])) {
                    return;
                }
            }

            $oUser->changeUserData($sUserName, $sPassword, $sPassword2, $aInvAdress, $aDelAdress);
            // assigning to newsletter
            if (($blOptin = Registry::getConfig()->getRequestParameter('blnewssubscribed')) === null) {
                $blOptin = $oUser->getNewsSubscription()->getOptInStatus();
            }
            // check if email address changed, if so, force check newsletter subscription settings.
            $sBillingUsername = $aInvAdress['oxuser__oxusername'];
            $blForceCheckOptIn = ($sBillingUsername !== null && $sBillingUsername !== $sUserName);
            $blEmailParam = $this->getConfig()->getConfigParam('blOrderOptInEmail');
            $this->_blNewsSubscriptionStatus = $oUser->setNewsSubscription($blOptin, $blEmailParam, $blForceCheckOptIn);
        } catch (\OxidEsales\Eshop\Core\Exception\UserException $oEx) { // errors in input
            // marking error code
            //TODO
            Registry::getUtilsView()->addErrorToDisplay($oEx, false, true);

            return;
        } catch (\OxidEsales\Eshop\Core\Exception\InputException $oEx) {
            Registry::getUtilsView()->addErrorToDisplay($oEx, false, true);
            Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, 'input_not_all_fields');

            return;
        } catch (\OxidEsales\Eshop\Core\Exception\ConnectionException $oEx) {
            //connection to external resource broken, change message and pass to the view
            Registry::getUtilsView()->addErrorToDisplay($oEx, false, true);

            return;
        }

        $this->resetPermissions();

        // order remark
        $sOrderRemark = Registry::getConfig()->getRequestParameter('order_remark', true);

        if ($sOrderRemark) {
            Registry::getSession()->setVariable('ordrem', $sOrderRemark);
        } else {
            Registry::getSession()->deleteVariable('ordrem');
        }

        if ($oBasket = $this->getSession()->getBasket()) {
            $oBasket->setBasketUser(null);
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
        $blShowShipAddressParameter = Registry::getConfig()->getRequestParameter('blshowshipaddress');
        $blShowShipAddressVariable = Registry::getSession()->getVariable('blshowshipaddress');
        $sDeliveryAddressParameter = Registry::getConfig()->getRequestParameter('deladr', true);
        $aDeladr = ($blShowShipAddressParameter || $blShowShipAddressVariable) ? $sDeliveryAddressParameter : [];
        $aDelAdress = $aDeladr;

        if (is_array($aDeladr)) {
            // checking if data is filled
            if (isset($aDeladr['oxaddress__oxsal'])) {
                unset($aDeladr['oxaddress__oxsal']);
            }
            if (!count($aDeladr) || implode('', $aDeladr) == '') {
                // resetting to avoid empty records
                $aDelAdress = [];
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

        $sLogoutLink = $oConfig->isSsl() ? $oConfig->getShopSecureHomeUrl() : $oConfig->getShopHomeUrl();
        $sLogoutLink .= 'cl=' . $oConfig->getRequestControllerId() . $this->getParent()->getDynUrlParams();
        if ($sParam = $oConfig->getRequestParameter('anid')) {
            $sLogoutLink .= '&amp;anid=' . $sParam;
        }
        if ($sParam = $oConfig->getRequestParameter('cnid')) {
            $sLogoutLink .= '&amp;cnid=' . $sParam;
        }
        if ($sParam = $oConfig->getRequestParameter('mnid')) {
            $sLogoutLink .= '&amp;mnid=' . $sParam;
        }
        if ($sParam = $oConfig->getRequestParameter('tpl')) {
            $sLogoutLink .= '&amp;tpl=' . $sParam;
        }
        if ($sParam = $oConfig->getRequestParameter('oxloadid')) {
            $sLogoutLink .= '&amp;oxloadid=' . $sParam;
        }
        // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
        if ($sParam = $oConfig->getRequestParameter('recommid')) {
            $sLogoutLink .= '&amp;recommid=' . $sParam;
        }
        // END deprecated

        return $sLogoutLink . '&amp;fnc=logout';
    }

    /**
     * Sets user login state
     *
     * @param int $iStatus login state (USER_LOGIN_SUCCESS/USER_LOGIN_FAIL/USER_LOGOUT)
     */
    public function setLoginStatus($iStatus)
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
     * Sets invitor id to session from URL
     */
    public function getInvitor()
    {
        $sSu = Registry::getSession()->getVariable('su');

        if (!$sSu && ($sSuNew = Registry::getConfig()->getRequestParameter('su'))) {
            Registry::getSession()->setVariable('su', $sSuNew);
        }
    }

    /**
     * sets from URL invitor id
     */
    public function setRecipient()
    {
        $sRe = Registry::getSession()->getVariable('re');
        if (!$sRe && ($sReNew = Registry::getConfig()->getRequestParameter('re'))) {
            Registry::getSession()->setVariable('re', $sReNew);
        }
    }

    /**
     * @param array                   $address
     * @param AbstractUpdatableFields $updatableFields
     *
     * @return array
     */
    private function cleanAddress($address, $updatableFields)
    {
        if (is_array($address)) {
            /** @var UpdatableFieldsConstructor $updatableFieldsConstructor */
            $updatableFieldsConstructor = oxNew(UpdatableFieldsConstructor::class);
            $cleaner = $updatableFieldsConstructor->getAllowedFieldsCleaner($updatableFields);
            return $cleaner->filterByUpdatableFields($address);
        }

        return $address;
    }

    /**
     * Returns trimmed address.
     *
     * @param array $address
     *
     * @return array
     */
    private function trimAddress($address)
    {
        if (is_array($address)) {
            $fields  = oxNew(FormFields::class, $address);
            $trimmer = oxNew(FormFieldsTrimmer::class);

            $address = (array)$trimmer->trim($fields);
        }

        return $address;
    }

    /**
     * check if this user is guest user then delete user
     *
     * @param string $username
     *
     * @return bool
     * @throws Exception
     */
    private function deleteGuestUser(string $username): bool
    {
        $user = oxNew(User::class);
        $user->load($user->getIdByUserName($username));

        if ($user) {
            if (isset($user->oxuser__oxpassword->value) && empty($user->oxuser__oxpassword->value)) {
                if (!$user->delete()) {
                    return false;
                }
            }
        }

        return true;
    }
}
