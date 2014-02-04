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

DEFINE('_DB_SESSION_HANDLER', getShopBasePath() . 'core/adodblite/session/adodb-session.php');

/**
 * Session manager.
 * Performs session managing function, such as variables deletion,
 * initialisation and other session functions.
 * @package core
 */
class oxSession extends oxSuperCfg
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
     * @var string
     */
    protected  $_sId     = null;

    /**
     * A flag indicating that session was just created, useful for tracking cookie support
     *
     * @var bool
     */
    protected static $_blIsNewSession = false;

    /**
     * Singleton instance keeper.
     */
    protected static $_instance = null;

    /**
     * Active session user object
     * @var object
     */
    protected static  $_oUser = null;

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
    protected $_aRequireSessionWithParams = array(
                       'cl' => array (
                            'register' => true,
                            'account'  => true,
                           ),
                       'fnc' => array (
                           'tobasket'         => true,
                           'login_noredirect' => true,
                           'tocomparelist'    => true,
                           ),
                       '_artperpage' => true,
                       'ldtype'      => true,
                       'listorderby' => true,
    );

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
    protected $_aPersistentParams = array("actshop", "lang", "currency", "language", "tpllanguage");

    /**
     * get oxSession object instance (create if needed)
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::getSession() instead.
     *
     * @return oxSession
     */
    public static function getInstance()
    {
        return oxRegistry::getSession();
    }

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
     *
     * @return null
     */
    public function setId($sVal)
    {
        $this->_sId = $sVal;
    }

    /**
     * Sets session param name
     *
     * @param string $sVal name value
     *
     * @return null
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
     * Starts shop session, generates unique session ID, extracts user IP.
     *
     * @return null
     */
    public function start()
    {
        $myConfig = $this->getConfig();
        $sid = null;

        if ( $this->isAdmin() ) {
            $this->setName("admin_sid");
        } else {
            $this->setName("sid");
        }

        $sForceSidParam = $myConfig->getRequestParameter( $this->getForcedName() );
        $sSidParam = $myConfig->getRequestParameter( $this->getName() );

        //forcing sid for SSL<->nonSSL transitions
        if ($sForceSidParam) {
            $sid = $sForceSidParam;
        } elseif ($this->_getSessionUseCookies() && $this->_getCookieSid()) {
            $sid = $this->_getCookieSid();
        } elseif ($sSidParam) {
            $sid = $sSidParam;
        }

        //starting session if only we can
        if ( $this->_allowSessionStart() ) {

            //creating new sid
            if ( !$sid ) {
                self::$_blIsNewSession = true;
                $this->initNewSession();
            } else {
                self::$_blIsNewSession = false;
                $this->_setSessionId( $sid );
                $this->_sessionStart();
            }

            //special handling for new ZP cluster session, as in that case session_start() regenerates id
            if ( $this->_sId != session_id() ) {
                $this->_setSessionId( session_id() );
            }

            //checking for swapped client
            $blSwapped = $this->_isSwappedClient();
            if ( !self::$_blIsNewSession && $blSwapped ) {
                $this->initNewSession();

                // passing notification about session problems
                if ( $this->_sErrorMsg && $myConfig->getConfigParam( 'iDebug' ) ) {
                    oxRegistry::get("oxUtilsView")->addErrorToDisplay( oxNew( "oxException", $this->_sErrorMsg ) );
                }
            } elseif ( !$blSwapped ) {
                // transferring cookies between hosts
                oxRegistry::get("oxUtilsServer")->loadSessionCookies();
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
        return preg_replace('/[^a-z0-9]/i', '', $this->getConfig()->getRequestParameter( 'stoken') );
    }

    /**
     * retrieve session challenge token from session
     *
     * @return string
     */
    public function getSessionChallengeToken()
    {
        $sRet = preg_replace('/[^a-z0-9]/i', '', $this->getVariable( 'sess_stoken' ) );
        if (!$sRet) {
            $this->_initNewSessionChallenge();
            $sRet = $this->getVariable( 'sess_stoken' );
        }
        return $sRet;
    }

    /**
     * check for CSRF, returns true, if request (get/post) token maches session saved var
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
     *
     * @return null
     */
    protected function _initNewSessionChallenge()
    {
        $this->setVariable('sess_stoken', sprintf('%X', crc32(oxUtilsObject::getInstance()->generateUID())));
    }

    /**
     * Initialize session data (calls php::session_start())
     *
     * @return null
     */
    protected function _sessionStart()
    {
        $blSetNoCache = true;
        if ( $blSetNoCache ) {
            //enforcing no caching when session is started
            session_cache_limiter( 'nocache' );

            //cache limiter workaround for AOL browsers
            //as suggested at http://ilia.ws/archives/59-AOL-Browser-Woes.html
            if ( isset( $_SERVER['HTTP_USER_AGENT'] ) &&
                 strpos( $_SERVER['HTTP_USER_AGENT'], 'AOL' ) !== false ) {

                session_cache_limiter(false);
                header("Cache-Control: no-store, private, must-revalidate, proxy-revalidate, post-check=0, pre-check=0, max-age=0, s-maxage=0");
            }
        }

        // Including database session managing class if needed.
        if (oxRegistry::getConfig()->getConfigParam( 'blAdodbSessionHandler' ) ) {
            $oDB = oxDb::getDb();
            include_once _DB_SESSION_HANDLER;
        }

        $this->_blStarted = @session_start();
        if ( !$this->getSessionChallengeToken() ) {
            $this->_initNewSessionChallenge();
        }

        return $this->_blStarted;
    }

    /**
     * Assigns new session ID, clean existing data except persistent.
     *
     * @return null
     */
    public function initNewSession()
    {
        // starting session only if it was not started yet
        if ( self::$_blIsNewSession ) {
            $this->_sessionStart();
        }

        //saving persistent params if old session exists
        $aPersistent = array();
        foreach ( $this->_aPersistentParams as $sParam ) {
            if ( ( $sValue = $this->getVariable( $sParam ) ) ) {
                $aPersistent[$sParam] = $sValue;
            }
        }

        $this->_setSessionId( $this->_getNewSessionId() );

        //restoring persistent params to session
        foreach ( $aPersistent as $sKey => $sParam ) {
            $this->setVariable( $sKey, $aPersistent[$sKey] );
        }

        $this->_initNewSessionChallenge();

        // (re)setting actual user agent when initiating new session
        $this->setVariable( "sessionagent", oxRegistry::get("oxUtilsServer")->getServerVar( 'HTTP_USER_AGENT' ) );
    }

    /**
     * Regenerates session id
     *
     * @return null
     */
    public function regenerateSessionId()
    {
        // starting session only if it was not started yet
        if ( self::$_blIsNewSession ) {
            $this->_sessionStart();

            // (re)setting actual user agent when initiating new session
            $this->setVariable( "sessionagent", oxRegistry::get("oxUtilsServer")->getServerVar( 'HTTP_USER_AGENT' ) );
        }

        $this->_setSessionId( $this->_getNewSessionId( false ) );
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
    protected function _getNewSessionId( $blUnset = true )
    {
        $sOldId = session_id();
        @session_regenerate_id( ! oxRegistry::getConfig()->getConfigParam( 'blAdodbSessionHandler' ) );
        $sNewId = session_id();

        if ( $blUnset ) {
            session_unset();
        }

        if ( oxRegistry::getConfig()->getConfigParam( 'blAdodbSessionHandler' ) ) {
            $oDB = oxDb::getDb();
            $oDB->execute("UPDATE oxsessions SET SessionID = ".$oDB->quote( $sNewId )." WHERE SessionID = ".$oDB->quote( $sOldId ) );
        }

        return session_id();
    }

    /**
     * Ends the current session and store session data.
     *
     * @return null
     */
    public function freeze()
    {
        // storing basket ..
        $this->setVariable( $this->_getBasketName(), serialize( $this->getBasket() ) );

        session_write_close();
    }

    /**
     * Destroys all data registered to a session.
     *
     * @return null
     */
    public function destroy()
    {
        //session_unset();
        unset($_SESSION);
        session_destroy();
    }

    /**
     * Checks if variable is set in session. Returns true on success.
     *
     * @param string $name Name to check
     *
     * @deprecated since v5.0.0 (2012-08-27); Use public hasVariable()
     *
     * @return bool
     */
    public static function hasVar( $name )
    {
        return oxRegistry::getSession()->hasVariable( $name );
    }

    /**
     * Checks if variable is set in session. Returns true on success.
     *
     * @param string $name Name to check
     *
     * @return bool
     */
    public function hasVariable( $name )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( modSession::$unitMOD ) && is_object( modSession::$unitMOD ) ) {
                try {
                    $sVal = modSession::getInstance()->getVar( $name );
                    return isset( $sVal );
                } catch( Exception $e ) {
                    // if exception is thrown, use default
                }
            }
        }

        return isset( $_SESSION[$name] );
    }

    /**
     * Sets parameter and its value to global session mixedvar array.
     *
     * @param string $name  Name of parameter to store
     * @param mixed  $value Value of parameter
     *
     * @deprecated since v5.0.0 (2012-08-27); Use public setVariable()
     *
     * @return null
     */
    public static function setVar( $name, $value )
    {

        return oxRegistry::getSession()->setVariable( $name, $value );
    }

    /**
     * Sets parameter and its value to global session mixedvar array.
     *
     * @param string $name  Name of parameter to store
     * @param mixed  $value Value of parameter
     *
     * @return null
     */
    public function setVariable( $name, $value )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( modSession::$unitMOD ) && is_object( modSession::$unitMOD ) ) {
                try{
                    return modSession::getInstance()->setVar( $name, $value );
                } catch( Exception $e ) {
                    // if exception is thrown, use default
                }
            }
        }

        $_SESSION[$name] = $value;
        //logger( "set sessionvar : $name -> $value");
    }

    /**
     * IF available returns value of parameter, stored in session array.
     *
     * @param string $name Name of parameter
     *
     * @deprecated since v5.0.0 (2012-08-27); Use public getVariable()
     *
     * @return mixed
     */
    public static function getVar( $name )
    {
        return oxRegistry::getSession()->getVariable( $name );
    }

    /**
     * IF available returns value of parameter, stored in session array.
     *
     * @param string $name Name of parameter
     *
     * @return mixed
     */
    public function getVariable( $name )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( modSession::$unitMOD ) && is_object( modSession::$unitMOD ) ) {
                try{
                    return modSession::getInstance()->getVar( $name );
                } catch( Exception $e ) {
                    // if exception is thrown, use default
                }
            }
        }

        if ( isset( $_SESSION[$name] )) {
            return $_SESSION[$name];
        } else {
            return null;
        }
    }

    /**
     * Destroys a single element (passed to method) of an session array.
     *
     * @param string $name Name of parameter to destroy
     *
     * @deprecated since v5.0.0 (2012-08-27); Use public deleteVariable()
     *
     * @return null
     */
    public static function deleteVar( $name )
    {
        oxRegistry::getSession()->deleteVariable( $name );
    }

    /**
     * Destroys a single element (passed to method) of an session array.
     *
     * @param string $name Name of parameter to destroy
     *
     * @return null
     */
    public function deleteVariable( $name )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( modSession::$unitMOD ) && is_object( modSession::$unitMOD ) ) {
                try{
                    return modSession::getInstance()->setVar( $name, null );
                } catch( Exception $e ) {
                    // if exception is thrown, use default
                }
            }
        }

        $_SESSION[$name] = null;
        //logger( "delete sessionvar : $name");
        unset( $_SESSION[$name] );
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
    public function sid( $blForceSid = false )
    {
        $myConfig     = $this->getConfig();
        $blUseCookies = $this->_getSessionUseCookies();
        $sRet         = '';

        $blDisableSid = oxRegistry::getUtils()->isSearchEngine()
                        && is_array($myConfig->getConfigParam( 'aCacheViews' ) )
                        && !$this->isAdmin();

        //no cookie?
        if (!$blDisableSid && $this->getId() && ( $blForceSid || !$blUseCookies || !$this->_getCookieSid())) {
            $sRet = ( $blForceSid ? $this->getForcedName() : $this->getName() )."=".$this->getId();
        }

        if ($this->isAdmin()) {
            // admin mode always has to have token
            if ($sRet) {
                $sRet .= '&amp;';
            }
            $sRet .= 'stoken='.$this->getSessionChallengeToken();
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
             $sSid   = "<input type=\"hidden\" name=\"".$this->getForcedName()."\" value=\"". $this->getId() . "\" />";
        }
        if ($this->getId()) {
            $sToken = "<input type=\"hidden\" name=\"stoken\" value=\"".$this->getSessionChallengeToken(). "\" />";
        }
        return $sToken.$sSid;
    }

    /**
     * Returns basket session object.
     *
     * @return oxbasket
     */
    public function getBasket()
    {
        if ( $this->_oBasket === null ) {
            $sBasket = $this->getVariable( $this->_getBasketName() );

            //init oxbasketitem class first
            //#1746
            oxNew('oxbasketitem');

            // init oxbasket through oxNew and not oxAutoload, Mantis-Bug #0004262
            $oEmptyBasket = oxNew('oxbasket');
            
            $oBasket = ( $sBasket && ( $oBasket = unserialize( $sBasket ) ) ) ? $oBasket : null;

            if ( !$oBasket || ( get_class($oBasket) !== get_class($oEmptyBasket) ) ) {
                $oBasket = $oEmptyBasket;
            }

            $this->_validateBasket($oBasket);
            $this->setBasket( $oBasket );
        }

        return $this->_oBasket;
    }

    /**
     * Validate loaded from session basket content. Check for language change.
     *
     * @param oxBasket $oBasket Basket object loaded from session.
     *
     * @return null
     */
    protected function _validateBasket(oxBasket $oBasket)
    {
        $aCurrContent = $oBasket->getContents();
        if (empty($aCurrContent)) {
            return;
        }

        $iCurrLang = oxRegistry::getLang()->getBaseLanguage();
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
     *
     * @return null
     */
    public function setBasket( $oBasket )
    {
        // sets basket session object
        $this->_oBasket = $oBasket;
    }

    /**
     * Deletes basket session object.
     *
     * @return null
     */
    public function delBasket()
    {
        $this->setBasket( null );
        $this->deleteVariable( $this->_getBasketName());
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
     * when calling oxSession::start();
     *
     * @return bool
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
    public function isSidNeeded( $sUrl = null )
    {
        if ($this->isAdmin()) {
            return true;
        }

        $oConfig = $this->getConfig();

        if ( !$this->_getSessionUseCookies() || ( $sUrl && $this->_getCookieSid() && !$oConfig->isCurrentProtocol($sUrl) ) ) {
            // switching from ssl to non ssl or vice versa?
            return true;
        }

        if ( $sUrl && !$oConfig->isCurrentUrl( $sUrl ) ) {
            return true;
        } elseif ( $this->_blSidNeeded === null ) {
            // setting initial state
            $this->_blSidNeeded = false;

            // no SIDs for seach engines
            if ( !oxRegistry::getUtils()->isSearchEngine() ) {
                // cookie found - SID is not needed
                if ( oxRegistry::get("oxUtilsServer")->getOxCookie( $this->getName() ) ) {
                    $this->_blSidNeeded = false;
                } elseif ( $this->_forceSessionStart() ) {
                    $this->_blSidNeeded = true;
                } else {
                    // no cookie, so must check session
                    if ( $blSidNeeded = $this->getVariable( 'blSidNeeded' ) ) {
                        $this->_blSidNeeded = true;
                    } elseif ( $this->_isSessionRequiredAction() ) {

                        if (!count($_COOKIE)) {
                            $this->_blSidNeeded = true;

                            // storing to session, performance..
                            $this->setVariable( 'blSidNeeded', $this->_blSidNeeded  );
                        }
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
        $blReturn = (isset($_COOKIE[$this->getName()]) &&  ($_COOKIE[$this->getName()] == $this->getId()));
        return $blReturn;
    }

    /**
     * Appends url with session ID, but only if oxSession::_isSidNeeded() returns true
     * Direct usage of this method to retrieve end url result is discouraged - instead
     * see oxUtilsUrl::processUrl
     *
     * @param string $sUrl url to append with sid
     *
     * @see oxUtilsUrl::processUrl
     *
     * @return string
     */
    public function processUrl( $sUrl )
    {
        $blSid = $this->isSidNeeded( $sUrl );

        if ($blSid) {
            $sSid = $this->sid( $blSid );

            if ($sSid) {

                $oStr = getStr();
                $aUrlParts = explode( '#', $sUrl );
                if ( !$oStr->preg_match('/(\?|&(amp;)?)sid=/i', $aUrlParts[0]) && (false === $oStr->strpos($aUrlParts[0], $sSid))) {
                    if (!$oStr->preg_match('/(\?|&(amp;)?)$/', $sUrl)) {
                        $aUrlParts[0] .= ( $oStr->strstr( $aUrlParts[0], '?' ) !== false ?  '&amp;' : '?' );
                    }
                    $aUrlParts[0] .= $sSid . '&amp;';
                }
                $sUrl = join( '#', $aUrlParts );
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
        $sToken = $this->getVar('_rtoken');
        if (!$sToken && $blGenerateNew) {
            $sToken = md5(rand() . $this->getId());
            $sToken = substr($sToken, 0, 8);
            $this->setVariable( '_rtoken', $sToken );
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
        return ( !oxRegistry::getUtils()->isSearchEngine() ) && ( (( bool ) $this->getConfig()->getConfigParam( 'blForceSessionStart' )) || $this->getConfig()->getRequestParameter( "su" ) || $this->_blForceNewSession );
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
        if ( !$this->isAdmin() ) {
            if ( oxRegistry::getUtils()->isSearchEngine() || $myConfig->getRequestParameter( 'skipSession' ) ) {
                $blAllowSessionStart = false;
            } elseif (oxRegistry::get("oxUtilsServer")->getOxCookie( 'oxid_'.$myConfig->getShopId().'_autologin' ) === '1') {
                $blAllowSessionStart = true;
            } elseif ( !$this->_forceSessionStart() && !oxRegistry::get("oxUtilsServer")->getOxCookie( 'sid_key' ) ) {

                // session is not needed to start when it is not necessary:
                // - no sid in request and also user executes no session connected action
                // - no cookie set and user executes no session connected action
                if ( !oxRegistry::get("oxUtilsServer")->getOxCookie( $this->getName() ) &&
                     !( $myConfig->getRequestParameter( $this->getName() ) || $myConfig->getRequestParameter( $this->getForcedName() ) ) &&
                     !$this->_isSessionRequiredAction() ) {
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
        $myUtilsServer = oxRegistry::get("oxUtilsServer");

        // check only for non search engines
        if ( !oxRegistry::getUtils()->isSearchEngine() && !$myUtilsServer->isTrustedClientIp() && !$this->_isValidRemoteAccessToken()) {

            $myConfig = $this->getConfig();

            // checking if session user agent matches actual
            $blSwapped = $this->_checkUserAgent( $myUtilsServer->getServerVar( 'HTTP_USER_AGENT' ), $this->getVariable( 'sessionagent' ) );
            if ( !$blSwapped ) {
                if ( $myConfig->getConfigParam( 'blAdodbSessionHandler' ) ) {
                    $blSwapped = $this->_checkSid();
                }

                if ( !$blSwapped ) {
                    $blDisableCookieCheck = $myConfig->getConfigParam( 'blDisableCookieCheck' );
                    $blUseCookies         = $this->_getSessionUseCookies();
                    if ( !$blDisableCookieCheck && $blUseCookies ) {
                        $blSwapped = $this->_checkCookies( $myUtilsServer->getOxCookie( 'sid_key' ), $this->getVariable( "sessioncookieisset" ) );
                    }
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
    protected function _checkUserAgent( $sAgent, $sExistingAgent )
    {
        $blCheck = false;

        // processing
        $oUtils = oxRegistry::get("oxUtilsServer");
        $sAgent = $oUtils->processUserAgentInfo( $sAgent );
        $sExistingAgent = $oUtils->processUserAgentInfo( $sExistingAgent );

        if ( $sAgent && $sAgent !== $sExistingAgent ) {
            if ( $sExistingAgent ) {
                $this->_sErrorMsg = "Different browser ({$sExistingAgent}, {$sAgent}), creating new SID...<br>";
            }
            $blCheck = true;
        }

        return $blCheck;
    }

    /**
     * Checking if this sid is old
     *
     * @return bool
     */
    protected function _checkSid()
    {
        $oDb = oxDb::getDb();
        //matze changed sesskey to SessionID because structure of oxsession changed!!
        $sSID = $oDb->getOne("select SessionID from oxsessions where SessionID = ".$oDb->quote( $this->getId() ));

        //2007-05-14
        //we check _blNewSession as well as this may be actually new session not written to db yet
        if ( !$this->_blNewSession && (!isset( $sSID) || !$sSID)) {
            // this means, that this session has expired in the past and someone uses this sid to reactivate it
            $this->_sErrorMsg = "Session has expired in the past and someone uses this sid to reactivate it, creating new SID...<br>";
            return true;
        }
        return false;
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
    protected function _checkCookies( $sCookieSid, $aSessCookieSetOnce )
    {
        $blSwapped = false;
        $myConfig  = $this->getConfig();
        $sCurrUrl  = $myConfig->isSsl() ? $myConfig->getSslShopUrl() : $myConfig->getShopUrl();

        $blSessCookieSetOnce = false;
        if ( is_array($aSessCookieSetOnce) && isset( $aSessCookieSetOnce[$sCurrUrl] ) ) {
            $blSessCookieSetOnce = $aSessCookieSetOnce[$sCurrUrl];
        }

        //if cookie was there once but now is gone it means we have to reset
        if ( $blSessCookieSetOnce && !$sCookieSid ) {
            if ( $myConfig->getConfigParam( 'iDebug' ) ) {
                $this->_sErrorMsg  = "Cookie not found, creating new SID...<br>";
                $this->_sErrorMsg .= "Cookie: $sCookieSid<br>";
                $this->_sErrorMsg .= "Session: $blSessCookieSetOnce<br>";
                $this->_sErrorMsg .= "URL: ".$sCurrUrl."<br>";
            }
            $blSwapped = true;
        }

        //if we detect the cookie then set session var for possible later use
        if ( $sCookieSid == "oxid" && !$blSessCookieSetOnce ) {
            if (!is_array($aSessCookieSetOnce)) {
                $aSessCookieSetOnce = array();
            }

            $aSessCookieSetOnce[$sCurrUrl] = "ox_true";
            $this->setVariable( "sessioncookieisset", $aSessCookieSetOnce );
        }

        //if we have no cookie then try to set it
        if ( !$sCookieSid ) {
            oxRegistry::get("oxUtilsServer")->setOxCookie( 'sid_key', 'oxid' );
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
        if ( $sSessId && session_id() != $sSessId ) {
            $this->_blNewSession = true;
        }

        session_id( $sSessId );

        $this->setId( $sSessId );

        $blUseCookies = $this->_getSessionUseCookies();

        if ( !$this->_allowSessionStart() ) {
            if ( $blUseCookies ) {
                oxRegistry::get("oxUtilsServer")->setOxCookie( $this->getName(), null );
            }
            return;
        }

        if ( $blUseCookies ) {
            //setting session cookie
            oxRegistry::get("oxUtilsServer")->setOxCookie( $this->getName(), $sSessId );
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
        if ( $myConfig->getConfigParam( 'blMallSharedBasket' ) == 0 ) {
            return $myConfig->getShopId()."_basket";
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
        return oxRegistry::get("oxUtilsServer")->getOxCookie($this->getName());
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
            $sValue = $this->getConfig()->getRequestParameter( $sParam );
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

        return ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == 'POST');
    }

    /**
     * return cookies usage for sid possibilities
     *
     * @return bool
     */
    protected function _getSessionUseCookies()
    {
        return $this->isAdmin() || $this->getConfig()->getConfigParam( 'blSessionUseCookies');
    }

    /**
     * Checks if token supplied over 'rtoken' parameter match remote accecss session token.
     *
     * @return bool
     */
    protected function _isValidRemoteAccessToken()
    {
        $sInputToken = $this->getConfig()->getRequestParameter( 'rtoken' );
        $sToken = $this->getRemoteAccessToken(false);
        $blTokenEqual = !(bool)strcmp($sInputToken, $sToken);
        $blValid = $sInputToken && $blTokenEqual;

        return $blValid;
    }

    /**
     * return basket reservations handler object
     *
     * @return oxBasketReservation
     */
    public function getBasketReservations()
    {
        if (!$this->_oBasketReservations) {
            $this->_oBasketReservations = oxNew('oxBasketReservation');
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


}
