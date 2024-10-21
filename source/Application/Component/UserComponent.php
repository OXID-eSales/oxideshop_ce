<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component;

use Exception;
use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Application\Model\User\UserShippingAddressUpdatableFields;
use OxidEsales\Eshop\Application\Model\User\UserUpdatableFields;
use OxidEsales\Eshop\Core\Contract\AbstractUpdatableFields;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Exception\ConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\InputException;
use OxidEsales\Eshop\Core\Exception\UserException;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Form\FormFields;
use OxidEsales\Eshop\Core\Form\FormFieldsTrimmer;
use OxidEsales\Eshop\Core\Form\UpdatableFieldsConstructor;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;

use function array_key_exists;
use function is_array;

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
        $this->saveDeliveryAddressState();
        $this->loadSessionUser();
        $this->saveInvitor();

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
        $this->checkPsState();

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
    protected function checkPsState()
    {
        $oConfig = Registry::getConfig();
        if ($this->getParent()->isEnabledPrivateSales()) {
            // load session user
            $oUser = $this->getUser();
            $sClass = $this->getParent()->getClassKey();

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
    protected function loadSessionUser()
    {
        $myConfig = Registry::getConfig();
        $session = Registry::getSession();

        $oUser = $this->getUser();

        // no session user
        if (!$oUser) {
            return;
        }

        // this user is blocked, deny him
        if ($oUser->inGroup('oxidblocked')) {
            $sUrl = $myConfig->getShopHomeUrl() . 'cl=content&tpl=user_blocked';
            Registry::getUtils()->redirect($sUrl, true, 302);
        }

        // TODO: move this to a proper place
        if ($oUser->isLoadedFromCookie() && !$myConfig->getConfigParam('blPerfNoBasketSaving')) {
            if ($oBasket = $session->getBasket()) {
                $oBasket->load();
                $oBasket->onUpdate();
            }
        }
    }

    /**
     * Collects posted user information from posted variables ("lgn_usr",
     * "lgn_pwd", "lgn_cook"), executes User::login() and checks if
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
        $sUser = Registry::getRequest()->getRequestEscapedParameter('lgn_usr');
        $sPassword = Registry::getRequest()->getRequestParameter('lgn_pwd');
        $sCookie = Registry::getRequest()->getRequestEscapedParameter('lgn_cook');

        $this->setLoginStatus(USER_LOGIN_FAIL);

        // trying to login user
        try {
            /** @var User $oUser */
            $oUser = oxNew(User::class);
            $oUser->login($sUser, $sPassword, $sCookie);
            $this->setLoginStatus(USER_LOGIN_SUCCESS);
        } catch (UserException $oEx) {
            // for login component send exception text to a custom component (if defined)
            Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, '', false);

            return 'user';
        } catch (\OxidEsales\Eshop\Core\Exception\CookieException $oEx) {
            Registry::getUtilsView()->addErrorToDisplay($oEx);

            return 'user';
        }

        // finalizing ..
        return $this->afterLogin($oUser);
    }

    /**
     * Special functionality which is performed after user logs in (or user is created without pass).
     * Performes additional checking if user is not BLOCKED
     * (User::InGroup("oxidblocked")) - if yes - redirects to blocked user
     * page ("cl=content&tpl=user_blocked").
     * Stores cookie info if user confirmed in login screen.
     * Then loads delivery info and forces basket to recalculate
     * (\OxidEsales\Eshop\Core\Session::getBasket() + oBasket::blCalcNeeded = true). Returns
     * "payment" to redirect to payment screen. If problems occured loading
     * user - sets error code according problem, and returns "user" to redirect
     * to user info screen.
     *
     * @param User $oUser user object
     *
     * @return string
     */
    protected function afterLogin($oUser)
    {
        $session = Registry::getSession();
        if ($session->isSessionStarted()) {
            $session->regenerateSessionId();
        }

        // this user is blocked, deny him
        if ($oUser->inGroup('oxidblocked')) {
            $sUrl = Registry::getConfig()->getShopHomeUrl() . 'cl=content&tpl=user_blocked';
            Registry::getUtils()->redirect($sUrl, true, 302);
        }

        // recalc basket
        if ($oBasket = $session->getBasket()) {
            $oBasket->onUpdate();
        }

        return 'payment';
    }

    /**
     * Executes oxcmp_user::login() method. After loggin user will not be
     * redirected to user or payment screens.
     */
    public function login_noredirect() //phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        $blAgb = Registry::getRequest()->getRequestEscapedParameter('ord_agb');

        if ($this->getParent()->isEnabledPrivateSales() && $blAgb !== null && ($oUser = $this->getUser())) {
            if ($blAgb) {
                $oUser->acceptTerms();
            }
        } else {
            $this->login();

            if (!$this->isAdmin() && !Registry::getConfig()->getConfigParam('blPerfNoBasketSaving')) {
                //load basket from the database
                try {
                    $session = Registry::getSession();
                    if ($oBasket = $session->getBasket()) {
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
    protected function afterLogout()
    {
        $session = Registry::getSession();

        $session->deleteVariable('paymentid');
        $session->deleteVariable('sShipSet');
        $session->deleteVariable('deladrid');
        $session->deleteVariable('dynvalue');

        // resetting & recalc basket
        if (($oBasket = $session->getBasket())) {
            $oBasket->resetUserInfo();
            $oBasket->onUpdate();

            // resetting voucher reservations
            if ($vouchers = $oBasket->getVouchers()) {
                foreach ($vouchers as $voucherId => $voucher) {
                    $oBasket->removeVoucher($voucherId);
                }
            }
        }

        $session->delBasket();
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
        $myConfig = Registry::getConfig();
        $oUser = oxNew(User::class);

        if ($oUser->logout()) {
            $this->setLoginStatus(USER_LOGOUT);

            // finalizing ..
            $this->afterLogout();

            $this->resetPermissions();

            if ($this->getParent()->isEnabledPrivateSales()) {
                return 'account';
            }

            // redirecting if user logs out in SSL mode
            if (Registry::getRequest()->getRequestEscapedParameter('redirect') && ContainerFacade::getParameter('oxid_shop_url')) {
                Registry::getUtils()->redirect($this->getLogoutLink());
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
     * Executes blUserRegistered = oxcmp_user::changeUserWithoutRedirect().
     * if this returns true - returns "payment" (this redirects to
     * payment page), else returns blUserRegistered value.
     *
     * @see oxcmp_user::changeUserWithoutRedirect()
     *
     * @return  mixed    redirection string or true if user is registered, false otherwise
     */
    public function changeUser()
    {
        return ($this->changeUserWithoutRedirect() === true) ? 'payment' : false;
    }

    /**
     * Executes oxcmp_user::changeUserWithoutRedirect().
     * returns "account_user" (this redirects to billing and shipping settings page) on success
     *
     * @return null
     */
    public function changeuser_testvalues() //phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        // skip updating user info if this is just form reload
        // on selecting delivery address
        // We do redirect only on success not to loose errors.

        if ($this->changeUserWithoutRedirect()) {
            return 'account_user';
        }
    }

    /**
     * First test if all required fields were filled, then performed
     * additional checking oxcmp_user::CheckValues(). If no errors
     * occured - trying to create new user (User::CreateUser()),
     * logging him to shop (User::Login() if user has entered password).
     * If User::CreateUser() returns false - this means user is
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
        if (!Registry::getSession()->checkSessionChallenge()) {
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_NON_MATCHING_CSRF_TOKEN');

            return false;
        }

        $isPrivateSales = $this->getParent()->isEnabledPrivateSales();

        if (
            $isPrivateSales
            && !Registry::getRequest()->getRequestEscapedParameter('ord_agb')
            && Registry::getConfig()->getConfigParam('blConfirmAGB')
        ) {
            Registry::getUtilsView()->addErrorToDisplay('READ_AND_CONFIRM_TERMS', false, true);

            return false;
        }

        $username = Registry::getRequest()->getRequestEscapedParameter('lgn_usr');
        $password = Registry::getRequest()->getRequestParameter('lgn_pwd');
        $passwordConfirmation = Registry::getRequest()->getRequestParameter('lgn_pwd2');

        $billingAddress = $this->getBillingAddress();
        $shippingAddress = $this->getShippingAddress();
        try {
            $user = oxNew(User::class);
            $user->checkValues($username, $password, $passwordConfirmation, $billingAddress, $shippingAddress);

            $user->oxuser__oxusername = new Field($username, Field::T_RAW);
            $user->setPassword($password);
            $user->oxuser__oxactive = new Field(
                $isPrivateSales ? 0 : 1,
                Field::T_RAW
            );

            $userSubscriptionStatus = $user->getNewsSubscription()->getOptInStatus();

            $database = DatabaseProvider::getDb();
            $database->startTransaction();
            try {
                $user->createUser();
                $user = $this->configureUserBeforeCreation($user);
                $user->load($user->getId());
                $user->changeUserData(
                    $user->oxuser__oxusername->value,
                    $password,
                    $password,
                    $billingAddress,
                    $shippingAddress
                );

                if ($isPrivateSales) {
                    $user->acceptTerms();
                }

                $database->commitTransaction();
            } catch (Exception $exception) {
                $database->rollbackTransaction();

                throw $exception;
            }

            $invitationSenderUserId = Registry::getSession()->getVariable("su");
            $invitationRecipientEmail = Registry::getSession()->getVariable("re");
            if (
                $invitationSenderUserId
                && $invitationRecipientEmail
                && Registry::getConfig()->getConfigParam('blInvitationsEnabled')
            ) {
                $user->setCreditPointsForRegistrant($invitationSenderUserId, $invitationRecipientEmail);
            }

            $isSubscriptionRequested = Registry::getRequest()->getRequestEscapedParameter('blnewssubscribed');
            if ($isSubscriptionRequested && $userSubscriptionStatus == 1) {
                // if user was assigned to newsletter
                // and is creating account with newsletter checked,
                // don't require confirm
                $user->getNewsSubscription()->setOptInStatus(1);
                $user->addToGroup('oxidnewsletter');
                $this->_blNewsSubscriptionStatus = 1;
            } else {
                $isSubscriptionEmailRequested = Registry::getConfig()->getConfigParam('blOrderOptInEmail');
                $this->_blNewsSubscriptionStatus = $user->setNewsSubscription(
                    $isSubscriptionRequested,
                    $isSubscriptionEmailRequested
                );
            }

            $user->addToGroup('oxidnotyetordered');
            $user->logout();
        } catch (UserException | ConnectionException | InputException | DatabaseConnectionException $exception) {
            Registry::getUtilsView()->addErrorToDisplay($exception, false, true);

            return false;
        }

        if (!$isPrivateSales) {
            Registry::getSession()->setVariable('usr', $user->getId());
            $this->setSessionLoginToken((string)$user->getFieldData('oxpassword'));
            $this->afterLogin($user);

            // order remark
            //V #427: order remark for new users
            $orderRemark = Registry::getRequest()->getRequestParameter('order_remark');
            if ($orderRemark) {
                Registry::getSession()->setVariable('ordrem', $orderRemark);
            }
        }

        if ((int)Registry::getRequest()->getRequestEscapedParameter('option') === 3) {
            $user->sendRegistrationEmail($isPrivateSales);
        }

        // new registered
        $this->_blIsNewUser = true;

        return $this->_blNewsSubscriptionStatus !== null && !$this->_blNewsSubscriptionStatus
            ? 'payment?new_user=1&success=1&newslettererror=4'
            : 'payment?new_user=1&success=1';
    }

    /**
     * If any additional configurations required right before user creation
     *
     * @param User $user
     *
     * @return User The user we gave in.
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
        $session = Registry::getSession();

        $addressId = Registry::getRequest()->getRequestEscapedParameter('oxaddressid');

        $address = oxNew(Address::class);
        $address->load($addressId);
        if ($this->canUserDeleteShippingAddress($address) && $session->checkSessionChallenge()) {
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
    protected function saveInvitor()
    {
        if (Registry::getConfig()->getConfigParam('blInvitationsEnabled')) {
            $this->getInvitor();
            $this->setRecipient();
        }
    }

    /**
     * Saving show/hide delivery address state
     */
    protected function saveDeliveryAddressState()
    {
        $oSession = Registry::getSession();

        $blShow = Registry::getRequest()->getRequestEscapedParameter('blshowshipaddress');
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
     * @return  bool|void true on success, false otherwise
     */
    protected function changeUserWithoutRedirect()
    {
        $session = Registry::getSession();

        if (!$session->checkSessionChallenge()) {
            return;
        }

        $user = $this->getUser();
        if (!$user) {
            return;
        }
        $shippingAddress = $this->getShippingAddress();
        $billingAddress = $this->getBillingAddress();

        $username = $user->getFieldData('oxusername');
        $password = $user->getFieldData('oxpassword');
        try {
            $newName = $billingAddress['oxuser__oxusername'] ?? '';
            if (
                $this->isGuestUser($user)
                && $this->isUserNameUpdated($user->oxuser__oxusername->value ?? '', $newName)
            ) {
                $this->deleteExistingGuestUser($newName);
            }
            $user->changeUserData($username, $password, $password, $billingAddress, $shippingAddress);

            $isSubscriptionRequested = Registry::getRequest()->getRequestEscapedParameter('blnewssubscribed');
            $userSubscriptionStatus = $isSubscriptionRequested
                ?? $user->getNewsSubscription()->getOptInStatus();
            // check if email address changed, if so, force check newsletter subscription settings.
            $billingUsername = $billingAddress['oxuser__oxusername'] ?? null;
            $forceSubscriptionCheck = ($billingUsername !== null && $billingUsername !== $username);
            $isSubscriptionEmailRequested = Registry::getConfig()->getConfigParam('blOrderOptInEmail');
            $this->_blNewsSubscriptionStatus = $user->setNewsSubscription(
                $userSubscriptionStatus,
                $isSubscriptionEmailRequested,
                $forceSubscriptionCheck
            );
        } catch (UserException | ConnectionException | InputException $exception) {
            Registry::getUtilsView()->addErrorToDisplay($exception, false, true);

            return;
        } catch (\Throwable) {
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_USER_UPDATE_FAILED', false, true);
            return false;
        }

        $this->resetPermissions();

        // order remark
        $orderRemark = Registry::getRequest()->getRequestParameter('order_remark', true);

        if ($orderRemark) {
            $session->setVariable('ordrem', $orderRemark);
        } else {
            $session->deleteVariable('ordrem');
        }

        if ($basket = $session->getBasket()) {
            $basket->setBasketUser(null);
            $basket->onUpdate();
        }

        return true;
    }

    /**
     * Returns delivery address from request. Before returning array is checked if
     * all needed data is there
     *
     * @return array
     */
    protected function getDelAddressData()
    {
        // if user company name, user name and additional info has special chars
        $blShowShipAddressParameter = Registry::getRequest()->getRequestEscapedParameter('blshowshipaddress');
        $blShowShipAddressVariable = Registry::getSession()->getVariable('blshowshipaddress');
        $sDeliveryAddressParameter = Registry::getRequest()->getRequestParameter('deladr');
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
    protected function getLogoutLink()
    {
        $oConfig = Registry::getConfig();

        $sLogoutLink = $oConfig->isSsl() ? $oConfig->getShopSecureHomeUrl() : $oConfig->getShopHomeUrl();
        $sLogoutLink .= 'cl=' . $oConfig->getRequestControllerId() . $this->getParent()->getDynUrlParams();
        if ($sParam = Registry::getRequest()->getRequestEscapedParameter('anid')) {
            $sLogoutLink .= '&amp;anid=' . $sParam;
        }
        if ($sParam = Registry::getRequest()->getRequestEscapedParameter('cnid')) {
            $sLogoutLink .= '&amp;cnid=' . $sParam;
        }
        if ($sParam = Registry::getRequest()->getRequestEscapedParameter('mnid')) {
            $sLogoutLink .= '&amp;mnid=' . $sParam;
        }
        if ($sParam = basename(Registry::getRequest()->getRequestEscapedParameter('tpl'))) {
            $sLogoutLink .= '&amp;tpl=' . $sParam;
        }
        if ($sParam = Registry::getRequest()->getRequestEscapedParameter('oxloadid')) {
            $sLogoutLink .= '&amp;oxloadid=' . $sParam;
        }
        // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
        if ($sParam = Registry::getRequest()->getRequestEscapedParameter('recommid')) {
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

        if (!$sSu && ($sSuNew = Registry::getRequest()->getRequestEscapedParameter('su'))) {
            Registry::getSession()->setVariable('su', $sSuNew);
        }
    }

    /**
     * sets from URL invitor id
     */
    public function setRecipient()
    {
        $sRe = Registry::getSession()->getVariable('re');
        if (!$sRe && ($sReNew = Registry::getRequest()->getRequestEscapedParameter('re'))) {
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

    private function isGuestUser(User $user): bool
    {
        return empty($user->oxuser__oxpassword->value);
    }

    private function isUserNameUpdated(string $currentName, string $newName): bool
    {
        return $currentName && $newName && $currentName !== $newName;
    }

    /**
     * @throws Exception
     */
    private function deleteExistingGuestUser(string $newName): void
    {
        $existingUser = oxNew(User::class);
        $existingUser->load($existingUser->getIdByUserName($newName));
        if ($existingUser && $this->isGuestUser($existingUser)) {
            $existingUser->delete();
        }
    }

    private function getShippingAddress(): ?array
    {
        $shippingAddress = $this->getDelAddressData();
        $shippingAddress = $this->cleanAddress($shippingAddress, oxNew(UserShippingAddressUpdatableFields::class));
        return $this->trimAddress($shippingAddress);
    }

    private function getBillingAddress(): ?array
    {
        $billingAddress = Registry::getRequest()->getRequestParameter('invadr');
        $billingAddress = $this->cleanAddress($billingAddress, oxNew(UserUpdatableFields::class));
        if ($billingAddress && is_array($billingAddress)) {
            $billingAddress = $this->removeNonAddressFields($billingAddress);
        }
        return $this->trimAddress($billingAddress);
    }

    private function removeNonAddressFields(array $addressFormData): array
    {
        $nonAddressFields = [
            'oxuser__oxactive',
            'oxuser__oxshopid',
            'oxuser__oxpassword',
            'oxuser__oxpasssalt',
            'oxuser__oxupdatekey',
            'oxuser__oxupdateexp',
        ];
        foreach ($nonAddressFields as $field) {
            if ($addressFormData && array_key_exists($field, $addressFormData)) {
                unset($addressFormData[$field]);
            }
        }

        return $addressFormData;
    }

    private function setSessionLoginToken(string $passwordHash): void
    {
        Registry::getSession()
            ->setVariable(
                'login-token',
                ContainerFacade::get(PasswordServiceBridgeInterface::class)->hash($passwordHash)
            );
    }
}
