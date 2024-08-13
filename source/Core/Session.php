<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Str;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;

/**
 * Session manager.
 * Performs session managing function, such as variables deletion,
 * initialisation and other session functions.
 */
class Session extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Session parameter name
     *
     * @var string
     */
    protected $_sName = 'sid';

    /**
     * Session parameter name
     *
     * @var string
     */
    protected $_sForcedPrefix = 'force_';

    /**
     * Unique session ID.
     *
     * @var string
     */
    protected $_sId = null;

    /**
     * A flag indicating that session was just created, useful for tracking cookie support
     *
     * @var bool
     */
    protected static $_blIsNewSession = false;

    /**
     * Active session user object
     *
     * @var object
     */
    protected static $_oUser = null;

    /**
     * Indicates if setting of session id is executed in this script. After page transition
     * This needed to be checked as new session is not written in db until it is closed
     *
     * @var bool
     */
    protected $_blNewSession = false;

    /**
     * Forces session to be started and skips checking if session is allowed
     *
     * @var bool
     */
    protected $_blForceNewSession = false;

    /**
     * Error message, used for debug purposes only
     *
     * @var string
     */
    protected $_sErrorMsg = null;

    /**
     * Basket session object
     *
     * @var object
     */
    protected $_oBasket = null;

    /**
     * Basket reservations object
     *
     * @var object
     */
    protected $_oBasketReservations = null;

    /**
     * Force session start by defined parameter rules.
     * First level array keys are parameters to check which point to
     * array of values which need session.
     *
     * @var array
     * @see _getRequireSessionWithParams()
     */
    protected $_aRequireSessionWithParams = [
        'cl'          => [
            'register' => true,
            'account'  => true,
        ],
        'fnc'         => [
            'tobasket'         => true,
            'login_noredirect' => true,
            'tocomparelist'    => true,
        ],
        '_artperpage' => true,
        'ldtype'      => true,
        'listorderby' => true,
    ];

    /**
     * Marker if processed urls must contain SID parameter
     *
     * @var bool
     */
    protected $_blSidNeeded = null;

    /**
     * Session params to be kept even after session timeout
     *
     * @var array
     */
    protected $_aPersistentParams = ["actshop", "lang", "currency", "language", "tpllanguage"];

    /**
     * Order steps which should not accept force_sid
     *
     * @var array
     */
    private $orderControllers = [
        'payment',
        'order',
        'thankyou'
    ];

    /**
     * Returns session ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->_sId;
    }

    /**
     * Sets session id
     *
     * @param string $sVal id value
     */
    public function setId($sVal)
    {
        $this->_sId = $sVal;
    }

    /**
     * Sets session param name
     *
     * @param string $sVal name value
     */
    public function setName($sVal)
    {
        $this->_sName = $sVal;
    }

    /**
     * Returns forced session id param name
     *
     * @return string
     */
    public function getForcedName()
    {
        return $this->_sForcedPrefix . $this->getName();
    }

    /**
     * Returns session param name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_sName;
    }

    /**
     * retrieves the session id from the request if any
     *
     * @return string|null
     */
    protected function getSidFromRequest()
    {
        $myConfig = Registry::getConfig();
        $sid = null;

        $forceSidParam = null;
        if (
            !$this->isForceSidBlocked() &&
            !in_array(Registry::getRequest()->getRequestEscapedParameter('cl'), $this->orderControllers)
        ) {
            $forceSidParam = Registry::getRequest()->getRequestEscapedParameter($this->getForcedName());
        }
        $sidParam = Registry::getRequest()->getRequestEscapedParameter($this->getName());

        //forcing sid for SSL<->nonSSL transitions
        if ($forceSidParam) {
            $sid = $forceSidParam;
        } elseif ($this->getSessionUseCookies() && $this->getCookieSid()) {
            $sid = $this->getCookieSid();
        } elseif ($sidParam) {
            $sid = $sidParam;
        }

        return $sid;
    }

    /**
     * Starts shop session, generates unique session ID, extracts user IP.
     *
     * @return void
     */
    public function start()
    {
        $this->setName($this->isAdmin() ? 'admin_sid' : 'sid');

        $sid = $this->getSidFromRequest();
        if ($sid) {
            $this->setId($sid);
        }

        if ($this->isSessionStarted() === false && $this->allowSessionStart()) {
            if (!$sid) {
                self::$_blIsNewSession = true;
                $this->initNewSession();
            } else {
                self::$_blIsNewSession = false;
                $this->setSessionId($sid);
                $this->sessionStart();
            }

            //special handling for new ZP cluster session, as in that case session_start() regenerates id
            if ($this->getId() !== session_id()) {
                $this->setId(session_id());
            }

            //checking for swapped client
            $blSwapped = $this->isSwappedClient();
            if (!self::$_blIsNewSession && $blSwapped) {
                $this->initNewSession();

                if ($this->_sErrorMsg && ContainerFacade::getParameter('oxid_debug_mode')) {
                    Registry::getUtilsView()->addErrorToDisplay(oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, $this->_sErrorMsg));
                }
            } elseif (!$blSwapped) {
                // transferring cookies between hosts
                Registry::getUtilsServer()->loadSessionCookies();
            }
        }
    }

    /**
     * retrieve session challenge token from request
     *
     * @return string
     */
    public function getRequestChallengeToken()
    {
        return preg_replace('/[^a-z0-9]/i', '', Registry::getRequest()->getRequestEscapedParameter('stoken') ?? '');
    }

    /**
     * retrieve session challenge token from session
     *
     * @return string
     */
    public function getSessionChallengeToken()
    {
        $sRet = preg_replace('/[^a-z0-9]/i', '', $this->getVariable('sess_stoken') ?? '');
        if (!$sRet) {
            $this->initNewSessionChallenge();
            $sRet = $this->getVariable('sess_stoken');
        }

        return $sRet;
    }

    /**
     * check for CSRF, returns true, if request (get/post) token matches session saved var
     * false, if CSRF is possible
     *
     * @return bool
     */
    public function checkSessionChallenge()
    {
        $sToken = $this->getSessionChallengeToken();

        return $sToken && ($sToken == $this->getRequestChallengeToken());
    }

    /**
     * initialize new session challenge token
     */
    protected function initNewSessionChallenge()
    {
        $this->setVariable('sess_stoken', sprintf('%X', crc32(Registry::getUtilsObject()->generateUID())));
    }

    /**
     * Initialize session data (calls php::session_start())
     *
     * @return bool
     */
    protected function sessionStart()
    {
        if ($this->needToSetHeaders()) {
            //enforcing no caching when session is started
            session_cache_limiter('nocache');
        } else {
            session_cache_limiter('');
        }

        session_start();

        if (!$this->getSessionChallengeToken()) {
            $this->initNewSessionChallenge();
        }

        return $this->isSessionStarted();
    }

    /**
     * Assigns new session ID, clean existing data except persistent.
     */
    public function initNewSession()
    {
        if (!$this->isSessionStarted()) {
            $this->sessionStart();
        }

        //saving persistent params if old session exists
        $aPersistent = [];
        foreach ($this->_aPersistentParams as $sParam) {
            if (($sValue = $this->getVariable($sParam))) {
                $aPersistent[$sParam] = $sValue;
            }
        }

        $sessionId = $this->getNewSessionId();
        $this->setId($sessionId);
        $this->setSessionCookie($sessionId);

        //restoring persistent params to session
        foreach ($aPersistent as $sKey => $sParam) {
            $this->setVariable($sKey, $aPersistent[$sKey]);
        }

        $this->initNewSessionChallenge();

        // (re)setting actual user agent when initiating new session
        $this->setVariable("sessionagent", Registry::getUtilsServer()->getServerVar('HTTP_USER_AGENT'));
    }

    /**
     * Regenerates session id
     */
    public function regenerateSessionId()
    {
        if (!$this->isSessionStarted()) {
            $this->sessionStart();

            // (re)setting actual user agent when initiating new session
            $this->setVariable("sessionagent", Registry::getUtilsServer()->getServerVar('HTTP_USER_AGENT'));
        }

        $sessionId = $this->getNewSessionId(false);
        $this->setId($sessionId);
        $this->setSessionCookie($sessionId);

        $this->initNewSessionChallenge();
    }

    /**
     * Update the current session id with a newly generated one, deletes the
     * old associated session file, frees all session variables.
     *
     * @param bool $blUnset if true, calls session_unset [optional]
     *
     * @return string
     */
    protected function getNewSessionId($blUnset = true)
    {
        session_regenerate_id(true);

        if ($blUnset) {
            session_unset();
        }

        return session_id();
    }

    /**
     * Ends the current session and store session data.
     */
    public function freeze()
    {
        // storing basket ..
        $this->setVariable($this->getBasketName(), serialize($this->getBasket()));

        session_write_close();
    }

    /**
     * Destroys all data registered to a session.
     */
    public function destroy()
    {
        unset($_SESSION);
        session_destroy();
    }

    /**
     * Checks if variable is set in session. Returns true on success.
     *
     * @param string $name Name to check
     *
     * @return bool
     */
    public function hasVariable($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Sets parameter and its value to global session mixedvar array.
     *
     * @param string $name  Name of parameter to store
     * @param mixed  $value Value of parameter
     */
    public function setVariable($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * IF available returns value of parameter, stored in session array.
     *
     * @param string $name Name of parameter
     *
     * @return mixed
     */
    public function getVariable($name)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    /**
     * Destroys a single element (passed to method) of an session array.
     *
     * @param string $name Name of parameter to destroy
     */
    public function deleteVariable($name)
    {
        $_SESSION[$name] = null;
        unset($_SESSION[$name]);
    }

    /**
     * Returns string prefix to URL with session ID parameter. In some cases
     * (if client is robot, such as Google) adds parameter shp, to identify,
     * witch shop is currently running.
     *
     * @param bool $blForceSid forces sid getter, ignores cookie check (optional)
     *
     * @return string
     */
    public function sid($blForceSid = false)
    {
        $myConfig = Registry::getConfig();
        $sRet = '';

        $blDisableSid = Registry::getUtils()->isSearchEngine()
                        && is_array($myConfig->getConfigParam('aCacheViews'))
                        && !$this->isAdmin();

        //no cookie?
        if (!$blDisableSid && $this->getId() && $this->canSendSidWithRequest($blForceSid)) {
            $sRet = ($blForceSid ? $this->getForcedName() : $this->getName()) . "=" . $this->getId();
        }

        if ($this->isAdmin()) {
            // admin mode always has to have token
            if ($sRet) {
                $sRet .= '&amp;';
            }
            $sRet .= 'stoken=' . $this->getSessionChallengeToken() . $this->getShopUrlId();
        }

        return $sRet;
    }

    /**
     * Forms input ("hidden" type) to pass session ID after submitting forms.
     *
     * @return string
     */
    public function hiddenSid()
    {
        $sSid = $sToken = '';
        if ($this->isSidNeeded()) {
            $sSid = "<input type=\"hidden\" name=\"" . $this->getName() . "\" value=\"" . $this->getId() . "\" />";
        }
        if ($this->getId()) {
            $sToken = "<input type=\"hidden\" name=\"stoken\" value=\"" . $this->getSessionChallengeToken() . "\" />";
        }

        return $sToken . $sSid;
    }

    /**
     * Returns basket session object.
     *
     * @return \OxidEsales\Eshop\Application\Model\Basket
     */
    public function getBasket()
    {
        if ($this->_oBasket === null) {
            $serializedBasket = $this->getVariable($this->getBasketName());

            //init oxbasketitem class first
            //#1746
            oxNew(BasketItem::class);

            // init oxbasket through oxNew and not oxAutoload, Mantis-Bug #0004262
            $emptyBasket = oxNew(Basket::class);

            $basket =
                $this->isSerializedBasketValid($serializedBasket) &&
                ($unserializedBasket = unserialize($serializedBasket)) &&
                $this->isUnserializedBasketValid($unserializedBasket, $emptyBasket) ?
                    $unserializedBasket : $emptyBasket;

            $this->validateBasket($basket);
            $this->setBasket($basket);
        }

        return $this->_oBasket;
    }

    /**
     * True if given serialized object is constructed with compatible classes.
     *
     * @param string $serializedBasket
     * @return bool
     */
    protected function isSerializedBasketValid($serializedBasket)
    {
        $basketClass = get_class(oxNew(Basket::class));
        $basketItemClass = get_class(oxNew(BasketItem::class));
        $priceClass = get_class(oxNew(\OxidEsales\Eshop\Core\Price::class));
        $priceListClass = get_class(oxNew(\OxidEsales\Eshop\Core\PriceList::class));
        $userClass = get_class(oxNew(User::class));

        return $serializedBasket &&
            $this->isClassInSerializedObject($serializedBasket, $basketClass) &&
            $this->isClassInSerializedObject($serializedBasket, $basketItemClass) &&
            $this->isClassOrNullInSerializedObjectAfterField($serializedBasket, "oPrice", $priceClass) &&
            $this->isClassOrNullInSerializedObjectAfterField($serializedBasket, "oProductsPriceList", $priceListClass) &&
            $this->isClassOrNullInSerializedObjectAfterField($serializedBasket, "oUser", $userClass);
    }

    /**
     * True if given class is found within serialized object.
     *
     * @param string $serializedObject
     * @param string $className
     *
     * @return bool
     */
    protected function isClassInSerializedObject($serializedObject, $className)
    {
        $quotedClassName = sprintf('"%s"', $className);

        return strpos($serializedObject, $quotedClassName) !== false;
    }

    /**
     * True if given class or null value is found after given field in serialized object.
     *
     * @param string $serializedObject
     * @param string $fieldName
     * @param string $className
     *
     * @return bool
     */
    protected function isClassOrNullInSerializedObjectAfterField($serializedObject, $fieldName, $className)
    {
        $fieldAndClassPattern = '/' . preg_quote($fieldName, '/') . '";((?P<null>N);|O:\d+:"(?P<class>[\w\\\\]+)":)/';
        $matchFound = preg_match($fieldAndClassPattern, $serializedObject, $matches) === 1;

        return $matchFound &&
            (
                (isset($matches['class']) && $matches['class'] === $className) ||
                (isset($matches['null']) && $matches['null'] === 'N')
            );
    }

    /**
     * True if both basket objects have been constructed from same class.
     *
     * Shop cannot function properly if provided with different basket class.
     *
     * @param \OxidEsales\Eshop\Application\Model\Basket $basket
     * @param \OxidEsales\Eshop\Application\Model\Basket $emptyBasket
     *
     * @return bool
     */
    protected function isUnserializedBasketValid($basket, $emptyBasket)
    {
        return $basket && (get_class($basket) === get_class($emptyBasket));
    }

    /**
     * Validate loaded from session basket content. Check for language change.
     *
     * @param \OxidEsales\Eshop\Application\Model\Basket $oBasket Basket object loaded from session.
     *
     * @return null
     */
    protected function validateBasket(\OxidEsales\Eshop\Application\Model\Basket $oBasket)
    {
        $aCurrContent = $oBasket->getContents();
        if (empty($aCurrContent)) {
            return;
        }

        $iCurrLang = Registry::getLang()->getBaseLanguage();
        foreach ($aCurrContent as $oContent) {
            if ($oContent->getLanguageId() != $iCurrLang) {
                $oContent->setLanguageId($iCurrLang);
            }
        }
    }

    /**
     * Sets basket session object.
     *
     * @param object $oBasket basket object
     */
    public function setBasket($oBasket)
    {
        // sets basket session object
        $this->_oBasket = $oBasket;
    }

    /**
     * Deletes basket session object.
     */
    public function delBasket()
    {
        $this->setBasket(null);
        $this->deleteVariable($this->getBasketName());
    }

    /**
     * Indicates if setting of session id is executed in this script.
     *
     * @return bool
     */
    public function isNewSession()
    {
        return self::$_blIsNewSession;
    }

    /**
     * Forces starting session and skips checking if session is allowed to start
     * when calling \OxidEsales\Eshop\Core\Session::start();
     */
    public function setForceNewSession()
    {
        $this->_blForceNewSession = true;
    }

    /**
     * Checks if cookies are not available. Returns TRUE of sid needed
     *
     * @param string $sUrl if passed domain does not match current - returns true (optional)
     *
     * @return bool
     */
    public function isSidNeeded($sUrl = null)
    {
        if ($this->isAdmin()) {
            return true;
        }

        $oConfig = Registry::getConfig();

        if (!$this->getSessionUseCookies() || ($sUrl && $this->getCookieSid() && !$oConfig->isCurrentProtocol($sUrl))) {
            // switching from ssl to non ssl or vice versa?
            return true;
        }

        if ($sUrl && !$oConfig->isCurrentUrl($sUrl)) {
            return true;
        }

        if ($sUrl && $oConfig->isCurrentUrl($sUrl)) {
            return false;
        }

        if ($this->_blSidNeeded === null) {
            // setting initial state
            $this->_blSidNeeded = false;

            // no SIDs for search engines
            if (!Registry::getUtils()->isSearchEngine()) {
                // cookie found - SID is not needed
                if (Registry::getUtilsServer()->getOxCookie($this->getName())) {
                    $this->_blSidNeeded = false;
                } elseif ($this->forceSessionStart()) {
                    $this->_blSidNeeded = true;
                } else {
                    // no cookie, so must check session
                    if ($blSidNeeded = $this->getVariable('blSidNeeded')) {
                        $this->_blSidNeeded = true;
                    } elseif ($this->isSessionRequiredAction() && !count($_COOKIE)) {
                        $this->_blSidNeeded = true;

                        // storing to session, performance..
                        $this->setVariable('blSidNeeded', $this->_blSidNeeded);
                    }
                }
            }
        }

        return $this->_blSidNeeded;
    }

    /**
     * Checks if current session id is the same as in originally received cookie.
     * This method is intended to indicate if new session cookie
     * is to be sent as header from this script execution.
     *
     * @return bool
     */
    public function isActualSidInCookie()
    {
        return isset($_COOKIE[$this->getName()]) && ($_COOKIE[$this->getName()] == $this->getId());
    }

    /**
     * Appends url with session ID, but only if \OxidEsales\Eshop\Core\Session::_isSidNeeded() returns true
     * Direct usage of this method to retrieve end url result is discouraged - instead
     * see \OxidEsales\Eshop\Core\UtilsUrl::processUrl
     *
     * @param string $sUrl url to append with sid
     *
     * @see \OxidEsales\Eshop\Core\UtilsUrl::processUrl
     *
     * @return string
     */
    public function processUrl($sUrl)
    {
        if ($this->isSidNeeded($sUrl)) {
            $sSid = $this->sid(true);
            if ($sSid) {
                $this->sidToUrlEvent();

                $oStr = Str::getStr();
                $aUrlParts = explode('#', $sUrl);
                if (!$oStr->preg_match('/(\?|&(amp;)?)sid=/i', $aUrlParts[0]) && (false === $oStr->strpos($aUrlParts[0], $sSid))) {
                    if (!$oStr->preg_match('/(\?|&(amp;)?)$/', $sUrl)) {
                        $aUrlParts[0] .= ($oStr->strstr($aUrlParts[0], '?') !== false ? '&amp;' : '?');
                    }
                    $aUrlParts[0] .= $sSid . '&amp;';
                }
                $sUrl = join('#', $aUrlParts);
            }
        }

        return $sUrl;
    }

    /**
     * Returns remote access key. With this key (called over "remotekey" URL parameter) and session id (sid parameter) you can access
     * session from another client.
     * The key is generated once per session after the first request.
     *
     * @param bool $blGenerateNew Should new token be generated
     *
     * @return string
     */
    public function getRemoteAccessToken($blGenerateNew = true)
    {
        $sToken = $this->getVariable('_rtoken');
        if (!$sToken && $blGenerateNew) {
            $sToken = md5(rand() . $this->getId());
            $sToken = substr($sToken, 0, 8);
            $this->setVariable('_rtoken', $sToken);
        }

        return $sToken;
    }

    /**
     * Returns true if its not search engine and config option blForceSessionStart = 1/true
     * or _GET parameter "su" (suggested user id) is set.
     *
     * @return bool
     */
    protected function forceSessionStart()
    {
        return !Registry::getUtils()->isSearchEngine() &&
            (
                ContainerFacade::getParameter('oxid_force_session_start') ||
                Registry::getRequest()->getRequestEscapedParameter('su') ||
                $this->_blForceNewSession
            );
    }

    /**
     * Checks if we can start new session. Returns bool success status
     *
     * @return bool
     */
    protected function allowSessionStart()
    {
        $blAllowSessionStart = true;
        $myConfig = Registry::getConfig();

        // special handling only in non-admin mode
        if (!$this->isAdmin()) {
            if (Registry::getUtils()->isSearchEngine() || Registry::getRequest()->getRequestEscapedParameter('skipSession')) {
                $blAllowSessionStart = false;
            } elseif (Registry::getUtilsServer()->getOxCookie('oxid_' . $myConfig->getShopId() . '_autologin') === '1') {
                $blAllowSessionStart = true;
            } elseif (!$this->forceSessionStart() && !Registry::getUtilsServer()->getOxCookie('sid_key')) {
                // session is not needed to start when it is not necessary:
                // - no sid in request and also user executes no session connected action
                // - no cookie set and user executes no session connected action
                if (
                    !Registry::getUtilsServer()->getOxCookie($this->getName()) &&
                    !$this->canTakeSidFromRequest() &&
                    !$this->isSessionRequiredAction()
                ) {
                    $blAllowSessionStart = false;
                }
            }
        }

        return $blAllowSessionStart;
    }

    /**
     * Saves various visitor parameters and compares with current data.
     * Returns true if any change is detected.
     * Using this method we can detect different visitor with same session id.
     *
     * @return bool
     */
    protected function isSwappedClient()
    {
        $blSwapped = false;
        $myUtilsServer = Registry::getUtilsServer();

        // check only for non search engines
        if (!Registry::getUtils()->isSearchEngine() && !$myUtilsServer->isTrustedClientIp() && !$this->isValidRemoteAccessToken()) {
            $myConfig = Registry::getConfig();

            // checking if session user agent matches actual
            $blSwapped = $this->checkUserAgent($myUtilsServer->getServerVar('HTTP_USER_AGENT'), $this->getVariable('sessionagent'));
            if (!$blSwapped) {
                $blDisableCookieCheck = $myConfig->getConfigParam('blDisableCookieCheck');
                $blUseCookies = $this->getSessionUseCookies();
                if (!$blDisableCookieCheck && $blUseCookies) {
                    $blSwapped = $this->checkCookies($myUtilsServer->getOxCookie('sid_key'), $this->getVariable("sessioncookieisset"));
                }
            }
        }

        return $blSwapped;
    }

    /**
     * Checking user agent
     *
     * @param string $sAgent         current user agent
     * @param string $sExistingAgent existing user agent
     *
     * @return bool
     */
    protected function checkUserAgent($sAgent, $sExistingAgent)
    {
        $blCheck = false;
        // processing
        $oUtils = Registry::getUtilsServer();
        $sAgent = $oUtils->processUserAgentInfo($sAgent);
        $sExistingAgent = $oUtils->processUserAgentInfo($sExistingAgent);

        if ($sAgent && $sAgent !== $sExistingAgent) {
            if ($sExistingAgent) {
                $this->_sErrorMsg = "Different browser ({$sExistingAgent}, {$sAgent}), creating new SID...<br>";
            }
            $blCheck = true;
        }

        return $blCheck;
    }

    /**
     * Check for existing cookie.
     * Cookie info is dropped from time to time.
     *
     * @param string $sCookieSid         coockie sid
     * @param array  $aSessCookieSetOnce if session cookie is set
     *
     * @return bool
     */
    protected function checkCookies($sCookieSid, $aSessCookieSetOnce)
    {
        $blSwapped = false;
        $myConfig = Registry::getConfig();
        $currUrl = $myConfig->getShopUrl();

        $blSessCookieSetOnce = false;
        if (is_array($aSessCookieSetOnce) && isset($aSessCookieSetOnce[$currUrl])) {
            $blSessCookieSetOnce = $aSessCookieSetOnce[$currUrl];
        }

        //if cookie was there once but now is gone it means we have to reset
        if ($blSessCookieSetOnce && !$sCookieSid) {
            if (ContainerFacade::getParameter('oxid_debug_mode')) {
                $this->_sErrorMsg = "Cookie not found, creating new SID...<br>";
                $this->_sErrorMsg .= "Cookie: $sCookieSid<br>";
                $this->_sErrorMsg .= "Session: $blSessCookieSetOnce<br>";
                $this->_sErrorMsg .= "URL: " . $currUrl . "<br>";
            }
            $blSwapped = true;
        }

        //if we detect the cookie then set session var for possible later use
        if ($sCookieSid == "oxid" && !$blSessCookieSetOnce) {
            if (!is_array($aSessCookieSetOnce)) {
                $aSessCookieSetOnce = [];
            }

            $aSessCookieSetOnce[$currUrl] = "ox_true";
            $this->setVariable("sessioncookieisset", $aSessCookieSetOnce);
        }

        //if we have no cookie then try to set it
        if (!$sCookieSid) {
            Registry::getUtilsServer()->setOxCookie('sid_key', 'oxid');
        }

        return $blSwapped;
    }

    /**
     * Sests session id to $sSessId
     *
     * @param string $sSessId sesion ID
     *
     * @return null
     */
    protected function setSessionId($sSessId)
    {
        //marking this session as new one, as it might be not writen to db yet
        if ($sSessId && session_id() != $sSessId) {
            $this->_blNewSession = true;
        }

        session_id($sSessId);

        $this->setId($sSessId);
        $this->setSessionCookie($sSessId);
    }

    /**
     * Returns name of shopping basket.
     *
     * @return string
     */
    protected function getBasketName()
    {
        return 'basket';
    }

    /**
     * Returns cookie sid value
     *
     * @return string
     */
    protected function getCookieSid()
    {
        return Registry::getUtilsServer()->getOxCookie($this->getName());
    }

    /**
     * returns configuration array with info which parameters require session
     * start
     *
     * @return array
     */
    protected function getRequireSessionWithParams()
    {
        $config = ContainerFacade::getParameter('oxid_session_init_params');
        $defaults = $this->_aRequireSessionWithParams;
        if (!$config) {
            return $defaults;
        }
        foreach ($config as $key => $val) {
            if ($val && !\is_array($val)) {
                unset($defaults[$key]);
            }
        }
        return array_replace_recursive($defaults, $config);
    }

    /**
     * Tests if current action requires session
     *
     * @return bool
     */
    protected function isSessionRequiredAction()
    {
        foreach ($this->getRequireSessionWithParams() as $sParam => $aValues) {
            $sValue = Registry::getRequest()->getRequestEscapedParameter($sParam);
            if (isset($sValue)) {
                if (is_array($aValues)) {
                    if (isset($aValues[$sValue]) && $aValues[$sValue]) {
                        return true;
                    }
                } elseif ($aValues) {
                    return true;
                }
            }
        }

        return (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST');
    }

    /**
     * return cookies usage for sid possibilities
     *
     * @return bool
     */
    protected function getSessionUseCookies()
    {
        return $this->isAdmin() || ContainerFacade::getParameter('oxid_cookies_session');
    }

    /**
     * Checks if token supplied over 'rtoken' parameter matches remote access session token.
     *
     * @return bool
     */
    protected function isValidRemoteAccessToken()
    {
        $inputToken = Registry::getRequest()->getRequestEscapedParameter('rtoken');
        $token = $this->getRemoteAccessToken(false);

        return !empty($inputToken) ? ($token === $inputToken) : false;
    }

    /**
     * return basket reservations handler object
     *
     * @return \OxidEsales\Eshop\Application\Model\BasketReservation
     */
    public function getBasketReservations()
    {
        if (!$this->_oBasketReservations) {
            $this->_oBasketReservations = oxNew(\OxidEsales\Eshop\Application\Model\BasketReservation::class);
        }

        return $this->_oBasketReservations;
    }

    /**
     * Checks if headers were already outputed
     *
     * @return bool
     */
    public function isHeaderSent()
    {
        return headers_sent();
    }

    /**
     * Returns true if session was started
     *
     * @return bool
     */
    public function isSessionStarted()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Return Shop IR parameter for Url.
     *
     * @return string
     */
    protected function getShopUrlId()
    {
        return '';
    }

    /**
     * Decide if need to set session headers to browser.
     *
     * @return bool
     */
    protected function needToSetHeaders()
    {
        return true;
    }

    /**
     * Place to hook when SID is added to URL.
     */
    protected function sidToUrlEvent()
    {
    }

    /**
     * Set session cookie
     *
     * @param string $sessionId   Session cookie value
     *
     * @return void
     */
    protected function setSessionCookie($sessionId): void
    {
        if ($this->getSessionUseCookies()) {
            if (!$this->allowSessionStart()) {
                Registry::getUtilsServer()->setOxCookie($this->getName(), null);
            } else {
                Registry::getUtilsServer()->setOxCookie($this->getName(), $sessionId);
            }
        }
    }

    private function isForceSidBlocked(): bool
    {
        return ContainerFacade::getParameter('oxid_disallow_force_session_id');
    }

    private function canSendSidWithRequest(bool $useForceSid): bool
    {
        return ($useForceSid || !$this->getSessionUseCookies() || !$this->getCookieSid())
            && !($useForceSid && $this->isForceSidBlocked());
    }

    private function canTakeSidFromRequest(): bool
    {
        return Registry::getRequest()->getRequestEscapedParameter($this->getName())
            || (
                Registry::getRequest()->getRequestEscapedParameter($this->getForcedName())
                && !$this->isForceSidBlocked()
            );
    }
}
