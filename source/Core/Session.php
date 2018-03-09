<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use \OxidEsales\Eshop\Application\Model\Basket;
use \OxidEsales\Eshop\Application\Model\BasketItem;
use \OxidEsales\Eshop\Application\Model\User;

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
     * Started session marker
     *
     * @var bool
     */
    protected $_blStarted = false;

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
        $myConfig = $this->getConfig();
        $sid = null;

        $sForceSidParam = $myConfig->getRequestParameter($this->getForcedName());
        $sSidParam = $myConfig->getRequestParameter($this->getName());

        //forcing sid for SSL<->nonSSL transitions
        if ($sForceSidParam) {
            $sid = $sForceSidParam;
        } elseif ($this->_getSessionUseCookies() && $this->_getCookieSid()) {
            $sid = $this->_getCookieSid();
        } elseif ($sSidParam) {
            $sid = $sSidParam;
        }

        return $sid;
    }

    /**
     * Starts shop session, generates unique session ID, extracts user IP.
     */
    public function start()
    {
        $myConfig = $this->getConfig();

        if ($this->isAdmin()) {
            $this->setName("admin_sid");
        } else {
            $this->setName("sid");
        }

        $sid = $this->getSidFromRequest();

        //starting session if only we can
        if ($this->_allowSessionStart()) {
            //creating new sid
            if (!$sid) {
                self::$_blIsNewSession = true;
                $this->initNewSession();
            } else {
                self::$_blIsNewSession = false;
                $this->_setSessionId($sid);
                $this->_sessionStart();
            }

            //special handling for new ZP cluster session, as in that case session_start() regenerates id
            if ($this->_sId != session_id()) {
                $this->_setSessionId(session_id());
            }

            //checking for swapped client
            $blSwapped = $this->_isSwappedClient();
            if (!self::$_blIsNewSession && $blSwapped) {
                $this->initNewSession();

                // passing notification about session problems
                if ($this->_sErrorMsg && $myConfig->getConfigParam('iDebug')) {
                    \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, $this->_sErrorMsg));
                }
            } elseif (!$blSwapped) {
                // transferring cookies between hosts
                \OxidEsales\Eshop\Core\Registry::getUtilsServer()->loadSessionCookies();
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
        return preg_replace('/[^a-z0-9]/i', '', $this->getConfig()->getRequestParameter('stoken'));
    }

    /**
     * retrieve session challenge token from session
     *
     * @return string
     */
    public function getSessionChallengeToken()
    {
        $sRet = preg_replace('/[^a-z0-9]/i', '', $this->getVariable('sess_stoken'));
        if (!$sRet) {
            $this->_initNewSessionChallenge();
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
    protected function _initNewSessionChallenge()
    {
        $this->setVariable('sess_stoken', sprintf('%X', crc32(\OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUID())));
    }

    /**
     * Initialize session data (calls php::session_start())
     *
     * @return null
     */
    protected function _sessionStart()
    {
        if ($this->needToSetHeaders()) {
            //enforcing no caching when session is started
            session_cache_limiter('nocache');

            //cache limiter workaround for AOL browsers
            //as suggested at http://ilia.ws/archives/59-AOL-Browser-Woes.html
            if (isset($_SERVER['HTTP_USER_AGENT']) &&
                strpos($_SERVER['HTTP_USER_AGENT'], 'AOL') !== false
            ) {
                session_cache_limiter(false);
                header("Cache-Control: no-store, private, must-revalidate, proxy-revalidate, post-check=0, pre-check=0, max-age=0, s-maxage=0");
            }
        } else {
            session_cache_limiter(false);
        }

        $this->_blStarted = @session_start();
        if (!$this->getSessionChallengeToken()) {
            $this->_initNewSessionChallenge();
        }

        return $this->_blStarted;
    }

    /**
     * Assigns new session ID, clean existing data except persistent.
     */
    public function initNewSession()
    {
        // starting session only if it was not started yet
        if (self::$_blIsNewSession) {
            $this->_sessionStart();
        }

        //saving persistent params if old session exists
        $aPersistent = [];
        foreach ($this->_aPersistentParams as $sParam) {
            if (($sValue = $this->getVariable($sParam))) {
                $aPersistent[$sParam] = $sValue;
            }
        }

        $this->_setSessionId($this->_getNewSessionId());

        //restoring persistent params to session
        foreach ($aPersistent as $sKey => $sParam) {
            $this->setVariable($sKey, $aPersistent[$sKey]);
        }

        $this->_initNewSessionChallenge();

        // (re)setting actual user agent when initiating new session
        $this->setVariable("sessionagent", \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getServerVar('HTTP_USER_AGENT'));
    }

    /**
     * Regenerates session id
     */
    public function regenerateSessionId()
    {
        // starting session only if it was not started yet
        if (self::$_blIsNewSession) {
            $this->_sessionStart();

            // (re)setting actual user agent when initiating new session
            $this->setVariable("sessionagent", \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getServerVar('HTTP_USER_AGENT'));
        }

        $this->_setSessionId($this->_getNewSessionId(false));
        $this->_initNewSessionChallenge();
    }

    /**
     * Update the current session id with a newly generated one, deletes the
     * old associated session file, frees all session variables.
     *
     * @param bool $blUnset if true, calls session_unset [optional]
     *
     * @return string
     */
    protected function _getNewSessionId($blUnset = true)
    {
        @session_regenerate_id(true);

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
        $this->setVariable($this->_getBasketName(), serialize($this->getBasket()));

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
        $myConfig = $this->getConfig();
        $blUseCookies = $this->_getSessionUseCookies();
        $sRet = '';

        $blDisableSid = \OxidEsales\Eshop\Core\Registry::getUtils()->isSearchEngine()
                        && is_array($myConfig->getConfigParam('aCacheViews'))
                        && !$this->isAdmin();

        //no cookie?
        if (!$blDisableSid && $this->getId() && ($blForceSid || !$blUseCookies || !$this->_getCookieSid())) {
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
            $sSid = "<input type=\"hidden\" name=\"" . $this->getForcedName() . "\" value=\"" . $this->getId() . "\" />";
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
            $serializedBasket = $this->getVariable($this->_getBasketName());

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

            $this->_validateBasket($basket);
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
        $fieldAndClassPattern = '/'. preg_quote($fieldName, '/') . '";((?P<null>N);|O:\d+:"(?P<class>[\w\\\\]+)":)/';
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
    protected function _validateBasket(\OxidEsales\Eshop\Application\Model\Basket $oBasket)
    {
        $aCurrContent = $oBasket->getContents();
        if (empty($aCurrContent)) {
            return;
        }

        $iCurrLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
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
        $this->deleteVariable($this->_getBasketName());
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

        $oConfig = $this->getConfig();

        if (!$this->_getSessionUseCookies() || ($sUrl && $this->_getCookieSid() && !$oConfig->isCurrentProtocol($sUrl))) {
            // switching from ssl to non ssl or vice versa?
            return true;
        }

        if ($sUrl && !$oConfig->isCurrentUrl($sUrl)) {
            return true;
        } elseif ($this->_blSidNeeded === null) {
            // setting initial state
            $this->_blSidNeeded = false;

            // no SIDs for search engines
            if (!\OxidEsales\Eshop\Core\Registry::getUtils()->isSearchEngine()) {
                // cookie found - SID is not needed
                if (\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie($this->getName())) {
                    $this->_blSidNeeded = false;
                } elseif ($this->_forceSessionStart()) {
                    $this->_blSidNeeded = true;
                } else {
                    // no cookie, so must check session
                    if ($blSidNeeded = $this->getVariable('blSidNeeded')) {
                        $this->_blSidNeeded = true;
                    } elseif ($this->_isSessionRequiredAction() && !count($_COOKIE)) {
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

                $oStr = getStr();
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
    protected function _forceSessionStart()
    {
        return (!\OxidEsales\Eshop\Core\Registry::getUtils()->isSearchEngine()) && ((( bool ) $this->getConfig()->getConfigParam('blForceSessionStart')) || $this->getConfig()->getRequestParameter("su") || $this->_blForceNewSession);
    }

    /**
     * Checks if we can start new session. Returns bool success status
     *
     * @return bool
     */
    protected function _allowSessionStart()
    {
        $blAllowSessionStart = true;
        $myConfig = $this->getConfig();

        // special handling only in non-admin mode
        if (!$this->isAdmin()) {
            if (\OxidEsales\Eshop\Core\Registry::getUtils()->isSearchEngine() || $myConfig->getRequestParameter('skipSession')) {
                $blAllowSessionStart = false;
            } elseif (\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie('oxid_' . $myConfig->getShopId() . '_autologin') === '1') {
                $blAllowSessionStart = true;
            } elseif (!$this->_forceSessionStart() && !\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie('sid_key')) {
                // session is not needed to start when it is not necessary:
                // - no sid in request and also user executes no session connected action
                // - no cookie set and user executes no session connected action
                if (!\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie($this->getName()) &&
                    !($myConfig->getRequestParameter($this->getName()) || $myConfig->getRequestParameter($this->getForcedName())) &&
                    !$this->_isSessionRequiredAction()
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
    protected function _isSwappedClient()
    {
        $blSwapped = false;
        $myUtilsServer = \OxidEsales\Eshop\Core\Registry::getUtilsServer();

        // check only for non search engines
        if (!\OxidEsales\Eshop\Core\Registry::getUtils()->isSearchEngine() && !$myUtilsServer->isTrustedClientIp() && !$this->_isValidRemoteAccessToken()) {
            $myConfig = $this->getConfig();

            // checking if session user agent matches actual
            $blSwapped = $this->_checkUserAgent($myUtilsServer->getServerVar('HTTP_USER_AGENT'), $this->getVariable('sessionagent'));
            if (!$blSwapped) {
                $blDisableCookieCheck = $myConfig->getConfigParam('blDisableCookieCheck');
                $blUseCookies = $this->_getSessionUseCookies();
                if (!$blDisableCookieCheck && $blUseCookies) {
                    $blSwapped = $this->_checkCookies($myUtilsServer->getOxCookie('sid_key'), $this->getVariable("sessioncookieisset"));
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
    protected function _checkUserAgent($sAgent, $sExistingAgent)
    {
        $blCheck = false;
        // processing
        $oUtils = \OxidEsales\Eshop\Core\Registry::getUtilsServer();
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
    protected function _checkCookies($sCookieSid, $aSessCookieSetOnce)
    {
        $blSwapped = false;
        $myConfig = $this->getConfig();
        $sCurrUrl = $myConfig->isSsl() ? $myConfig->getSslShopUrl() : $myConfig->getShopUrl();

        $blSessCookieSetOnce = false;
        if (is_array($aSessCookieSetOnce) && isset($aSessCookieSetOnce[$sCurrUrl])) {
            $blSessCookieSetOnce = $aSessCookieSetOnce[$sCurrUrl];
        }

        //if cookie was there once but now is gone it means we have to reset
        if ($blSessCookieSetOnce && !$sCookieSid) {
            if ($myConfig->getConfigParam('iDebug')) {
                $this->_sErrorMsg = "Cookie not found, creating new SID...<br>";
                $this->_sErrorMsg .= "Cookie: $sCookieSid<br>";
                $this->_sErrorMsg .= "Session: $blSessCookieSetOnce<br>";
                $this->_sErrorMsg .= "URL: " . $sCurrUrl . "<br>";
            }
            $blSwapped = true;
        }

        //if we detect the cookie then set session var for possible later use
        if ($sCookieSid == "oxid" && !$blSessCookieSetOnce) {
            if (!is_array($aSessCookieSetOnce)) {
                $aSessCookieSetOnce = [];
            }

            $aSessCookieSetOnce[$sCurrUrl] = "ox_true";
            $this->setVariable("sessioncookieisset", $aSessCookieSetOnce);
        }

        //if we have no cookie then try to set it
        if (!$sCookieSid) {
            \OxidEsales\Eshop\Core\Registry::getUtilsServer()->setOxCookie('sid_key', 'oxid');
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
    protected function _setSessionId($sSessId)
    {
        //marking this session as new one, as it might be not writen to db yet
        if ($sSessId && session_id() != $sSessId) {
            $this->_blNewSession = true;
        }

        session_id($sSessId);

        $this->setId($sSessId);

        $blUseCookies = $this->_getSessionUseCookies();

        if (!$this->_allowSessionStart()) {
            if ($blUseCookies) {
                \OxidEsales\Eshop\Core\Registry::getUtilsServer()->setOxCookie($this->getName(), null);
            }

            return;
        }

        if ($blUseCookies) {
            //setting session cookie
            \OxidEsales\Eshop\Core\Registry::getUtilsServer()->setOxCookie($this->getName(), $sSessId);
        }
    }

    /**
     * Returns name of shopping basket.
     *
     * @return string
     */
    protected function _getBasketName()
    {
        $myConfig = $this->getConfig();
        if ($myConfig->getConfigParam('blMallSharedBasket') == 0) {
            return $myConfig->getShopId() . "_basket";
        }

        return "basket";
    }

    /**
     * Returns cookie sid value
     *
     * @return string
     */
    protected function _getCookieSid()
    {
        return \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie($this->getName());
    }

    /**
     * returns configuration array with info which parameters require session
     * start
     *
     * @return array
     */
    protected function _getRequireSessionWithParams()
    {
        $aCfgArray = $this->getConfig()->getConfigParam('aRequireSessionWithParams');
        if (is_array($aCfgArray)) {
            $aDefault = $this->_aRequireSessionWithParams;
            foreach ($aCfgArray as $key => $val) {
                if (!is_array($val) && $val) {
                    unset($aDefault[$key]);
                }
            }

            return array_merge_recursive($aCfgArray, $aDefault);
        }

        return $this->_aRequireSessionWithParams;
    }

    /**
     * Tests if current action requires session
     *
     * @return bool
     */
    protected function _isSessionRequiredAction()
    {
        foreach ($this->_getRequireSessionWithParams() as $sParam => $aValues) {
            $sValue = $this->getConfig()->getRequestParameter($sParam);
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
    protected function _getSessionUseCookies()
    {
        return $this->isAdmin() || $this->getConfig()->getConfigParam('blSessionUseCookies');
    }

    /**
     * Checks if token supplied over 'rtoken' parameter matches remote access session token.
     *
     * @return bool
     */
    protected function _isValidRemoteAccessToken()
    {
        $inputToken = $this->getConfig()->getRequestParameter('rtoken');
        $token = $this->getRemoteAccessToken(false);

        return !empty($inputToken) ? ($token === $inputToken) : false;
    }

    /**
     * return basket reservations handler object
     *
     * @return oxBasketReservation
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
        return $this->_blStarted;
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
}
