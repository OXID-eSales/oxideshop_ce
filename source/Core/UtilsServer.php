<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\EshopCommunity\Application\Model\User;

/**
 * Server data manipulation class
 */
class UtilsServer extends \OxidEsales\Eshop\Core\Base
{
    /**
     * user cookies
     *
     * @var array
     */
    protected $_aUserCookie = [];

    /**
     * Session cookie parameter name
     *
     * @var string
     */
    protected $_sSessionCookiesName = 'aSessionCookies';

    /**
     * Session stored cookies
     *
     * @var array
     */
    protected $_sSessionCookies = [];

    /**
     * sets cookie
     *
     * @param string $sName       cookie name
     * @param string $sValue      value
     * @param int    $iExpire     expire time
     * @param string $sPath       The path on the server in which the cookie will be available on
     * @param string $sDomain     The domain that the cookie is available.
     * @param bool   $blToSession is true, records cookie information to session
     * @param bool   $blSecure    if true, transfer cookie only via SSL
     * @param bool   $blHttpOnly  if true, only accessible via HTTP
     *
     * @return bool
     */
    public function setOxCookie($sName, $sValue = "", $iExpire = 0, $sPath = '/', $sDomain = null, $blToSession = true, $blSecure = false, $blHttpOnly = true)
    {
        if ($blToSession && !$this->isAdmin()) {
            $this->_saveSessionCookie($sName, $sValue, $iExpire, $sPath, $sDomain);
        }

        if (defined('OXID_PHP_UNIT') || php_sapi_name() === 'cli') {
            // do NOT set cookies in php unit or in cli because it would issue warnings
            return;
        }
        $config = $this->getConfig();
        //if shop runs in https only mode we can set secure flag to all cookies
        $blSecure = $blSecure || ($config->isSsl() && $config->getSslShopUrl() == $config->getShopUrl());
        return setcookie(
            $sName,
            $sValue,
            $iExpire,
            $this->_getCookiePath($sPath),
            $this->_getCookieDomain($sDomain),
            $blSecure,
            $blHttpOnly
        );
    }

    protected $_blSaveToSession = null;

    /**
     * Checks if cookie must be saved to session in order to transfer it to different domain
     *
     * @return bool
     */
    protected function _mustSaveToSession()
    {
        if ($this->_blSaveToSession === null) {
            $this->_blSaveToSession = false;

            $myConfig = $this->getConfig();
            if ($sSslUrl = $myConfig->getSslShopUrl()) {
                $sUrl = $myConfig->getShopUrl();

                $sHost = parse_url($sUrl, PHP_URL_HOST);
                $sSslHost = parse_url($sSslUrl, PHP_URL_HOST);

                // testing if domains matches..
                if ($sHost != $sSslHost) {
                    $oUtils = \OxidEsales\Eshop\Core\Registry::getUtils();
                    $this->_blSaveToSession = $oUtils->extractDomain($sHost) != $oUtils->extractDomain($sSslHost);
                }
            }
        }

        return $this->_blSaveToSession;
    }

    /**
     * Returns session cookie key
     *
     * @param bool $blGet mode - true - get, false - set cookie
     *
     * @return string
     */
    protected function _getSessionCookieKey($blGet)
    {
        $blSsl = $this->getConfig()->isSsl();
        $sKey = $blSsl ? 'nossl' : 'ssl';

        if ($blGet) {
            $sKey = $blSsl ? 'ssl' : 'nossl';
        }

        return $sKey;
    }

    /**
     * Copies cookie info to session
     *
     * @param string $sName   cookie name
     * @param string $sValue  cookie value
     * @param int    $iExpire expiration time
     * @param string $sPath   cookie path
     * @param string $sDomain cookie domain
     */
    protected function _saveSessionCookie($sName, $sValue, $iExpire, $sPath, $sDomain)
    {
        if ($this->_mustSaveToSession()) {
            $aCookieData = ['value' => $sValue, 'expire' => $iExpire, 'path' => $sPath, 'domain' => $sDomain];

            $aSessionCookies = ( array ) \OxidEsales\Eshop\Core\Registry::getSession()->getVariable($this->_sSessionCookiesName);
            $aSessionCookies[$this->_getSessionCookieKey(false)][$sName] = $aCookieData;

            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable($this->_sSessionCookiesName, $aSessionCookies);
        }
    }

    /**
     * Stored all session cookie info to cookies
     */
    public function loadSessionCookies()
    {
        if (($aSessionCookies = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable($this->_sSessionCookiesName))) {
            $sKey = $this->_getSessionCookieKey(true);
            if (isset($aSessionCookies[$sKey])) {
                // writing session data to cookies
                foreach ($aSessionCookies[$sKey] as $sName => $aCookieData) {
                    $this->setOxCookie($sName, $aCookieData['value'], $aCookieData['expire'], $aCookieData['path'], $aCookieData['domain'], false);
                    $this->_sSessionCookies[$sName] = $aCookieData['value'];
                }

                // cleanup
                unset($aSessionCookies[$sKey]);
                \OxidEsales\Eshop\Core\Registry::getSession()->setVariable($this->_sSessionCookiesName, $aSessionCookies);
            }
        }
    }

    /**
     * Returns cookie path. If user did not set path, or set it to null, according to php
     * documentation empty string will be returned, marking to skip argument. Additionally
     * path can be defined in config.inc.php file as "sCookiePath" param. Please check cookie
     * documentation for more details about current parameter
     *
     * @param string $sPath user defined cookie path
     *
     * @return string
     */
    protected function _getCookiePath($sPath)
    {
        if ($aCookiePaths = $this->getConfig()->getConfigParam('aCookiePaths')) {
            // in case user wants to have shop specific setup
            $sShopId = $this->getConfig()->getShopId();
            $sPath = isset($aCookiePaths[$sShopId]) ? $aCookiePaths[$sShopId] : $sPath;
        }

        // from php doc: .. You may also replace an argument with an empty string ("") in order to skip that argument..
        return $sPath ? $sPath : "";
    }

    /**
     * Returns domain that cookie available. If user did not set domain, or set it to null, according to php
     * documentation empty string will be returned, marking to skip argument. Additionally domain can be defined
     * in config.inc.php file as "sCookieDomain" param. Please check cookie documentation for more details about
     * current parameter
     *
     * @param string $sDomain the domain that the cookie is available.
     *
     * @return string
     */
    protected function _getCookieDomain($sDomain)
    {
        $sDomain = $sDomain ? $sDomain : "";

        // on special cases, like separate domain for SSL, cookies must be defined on domain specific path
        // please have a look at
        if (!$sDomain) {
            if ($aCookieDomains = $this->getConfig()->getConfigParam('aCookieDomains')) {
                // in case user wants to have shop specific setup
                $sShopId = $this->getConfig()->getShopId();
                $sDomain = isset($aCookieDomains[$sShopId]) ? $aCookieDomains[$sShopId] : $sDomain;
            }
        }

        return $sDomain;
    }

    /**
     * Returns cookie $sName value.
     * If optional parameter $sName is not set then getCookie() returns whole cookie array
     *
     * @param string $sName cookie param name
     *
     * @return mixed
     */
    public function getOxCookie($sName = null)
    {
        $sValue = null;
        if ($sName && isset($_COOKIE[$sName])) {
            $sValue = \OxidEsales\Eshop\Core\Registry::getConfig()->checkParamSpecialChars($_COOKIE[$sName]);
        } elseif ($sName && !isset($_COOKIE[$sName])) {
            $sValue = isset($this->_sSessionCookies[$sName]) ? $this->_sSessionCookies[$sName] : null;
        } elseif (!$sName && isset($_COOKIE)) {
            $sValue = $_COOKIE;
        }

        return $sValue;
    }

    /**
     * Returns remote IP address
     *
     * @return string
     */
    public function getRemoteAddress()
    {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $sIP = $_SERVER["HTTP_X_FORWARDED_FOR"];
            $sIP = preg_replace('/,.*$/', '', $sIP);
        } elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $sIP = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $sIP = $_SERVER["REMOTE_ADDR"];
        }

        return $sIP;
    }

    /**
     * returns a server constant
     *
     * @param string $sServVar optional - which server var should be returned, if null returns whole $_SERVER
     *
     * @return mixed
     */
    public function getServerVar($sServVar = null)
    {
        $sValue = null;
        if (isset($_SERVER)) {
            if ($sServVar && isset($_SERVER[$sServVar])) {
                $sValue = $_SERVER[$sServVar];
            } elseif (!$sServVar) {
                $sValue = $_SERVER;
            }
        }

        return $sValue;
    }

    /**
     * Sets user info into cookie
     *
     * @param string  $userName     user name
     * @param string  $passwordHash password hash
     * @param int     $shopId       shop ID (default null)
     * @param integer $timeout      timeout value (default 31536000)
     * @param string  $salt
     */
    public function setUserCookie($userName, $passwordHash, $shopId = null, $timeout = 31536000, $salt = User::USER_COOKIE_SALT)
    {
        $myConfig = $this->getConfig();
        $shopId = $shopId ?? $myConfig->getShopId();
        $sSslUrl = $myConfig->getSslShopUrl();
        if (stripos($sSslUrl, 'https') === 0) {
            $blSsl = true;
        } else {
            $blSsl = false;
        }

        $this->_aUserCookie[$shopId] = $userName . '@@@' . crypt($passwordHash, $salt);
        $this->setOxCookie('oxid_' . $shopId, $this->_aUserCookie[$shopId], \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() + $timeout, '/', null, true, $blSsl);
        $this->setOxCookie('oxid_' . $shopId . '_autologin', '1', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() + $timeout, '/', null, true, false);
    }

    /**
     * Deletes user cookie data
     *
     * @param string $sShopId shop ID (default null)
     */
    public function deleteUserCookie($sShopId = null)
    {
        $myConfig = $this->getConfig();
        $sShopId = (!$sShopId) ? $this->getConfig()->getShopId() : $sShopId;
        $sSslUrl = $myConfig->getSslShopUrl();
        if (stripos($sSslUrl, 'https') === 0) {
            $blSsl = true;
        } else {
            $blSsl = false;
        }

        $this->_aUserCookie[$sShopId] = '';
        $this->setOxCookie('oxid_' . $sShopId, '', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - 3600, '/', null, true, $blSsl);
        $this->setOxCookie('oxid_' . $sShopId . '_autologin', '0', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - 3600, '/', null, true, false);
    }

    /**
     * Returns cookie stored used login data
     *
     * @param string $sShopId shop ID (default null)
     *
     * @return string
     */
    public function getUserCookie($sShopId = null)
    {
        $myConfig = parent::getConfig();
        $sShopId = (!$sShopId) ? $myConfig->getShopId() : $sShopId;
        // check for SSL connection
        if (!$myConfig->isSsl() && $this->getOxCookie('oxid_' . $sShopId . '_autologin') == '1') {
            $sSslUrl = rtrim($myConfig->getSslShopUrl(), '/') . $_SERVER['REQUEST_URI'];
            if (stripos($sSslUrl, 'https') === 0) {
                \OxidEsales\Eshop\Core\Registry::getUtils()->redirect($sSslUrl, true, 302);
            }
        }

        if (array_key_exists($sShopId, $this->_aUserCookie) && $this->_aUserCookie[$sShopId] !== null) {
            return $this->_aUserCookie[$sShopId] ? $this->_aUserCookie[$sShopId] : null;
        }

        return $this->_aUserCookie[$sShopId] = $this->getOxCookie('oxid_' . $sShopId);
    }

    /**
     * Checks if current client ip is in trusted IPs list.
     * IP list is defined in config file as "aTrustedIPs" parameter
     *
     * @return bool
     */
    public function isTrustedClientIp()
    {
        $blTrusted = false;
        $aTrustedIPs = ( array ) $this->getConfig()->getConfigParam("aTrustedIPs");
        if (count($aTrustedIPs)) {
            $blTrusted = in_array($this->getRemoteAddress(), $aTrustedIPs);
        }

        return $blTrusted;
    }

    /**
     * Removes MSIE(\s)?(\S)*(\s) from browser agent information
     *
     * @param string $sAgent browser user agent idenfitier
     *
     * @return string
     */
    public function processUserAgentInfo($sAgent)
    {
        if ($sAgent) {
            $sAgent = getStr()->preg_replace("/MSIE(\s)?(\S)*(\s)/", "", (string) $sAgent);
        }

        return $sAgent;
    }

    /**
     * Compares current URL to supplied string
     *
     * @param string $sURL URL
     *
     * @return bool true if $sURL is equal to current page URL
     */
    public function isCurrentUrl($sURL)
    {
        // Missing protocol, cannot proceed, assuming true.
        if (!$sURL || (strpos($sURL, "http") !== 0)) {
            return true;
        }

        $sServerHost = $this->getServerVar('HTTP_HOST');
        $blIsCurrentUrl = $this->_isCurrentUrl($sURL, $sServerHost);
        if (!$blIsCurrentUrl) {
            $sServerHost = $this->getServerVar('HTTP_X_FORWARDED_HOST');
            if ($sServerHost) {
                $blIsCurrentUrl = $this->_isCurrentUrl($sURL, $sServerHost);
            }
        }

        return $blIsCurrentUrl;
    }

    /**
      * Check if the given URL is same as used for request.
      * The URL in this context is the base address for the shop e.g. https://www.domain.com/shop/
      * the protocol is optional (www.domain.com/shop/)
      * but the protocol relative syntax (//www.domain.com/shop/) is not yet supported.
      *
      * @param string $sURL        URL to check if is same as request.
      * @param string $sServerHost request host.
      *
      * @return bool true if $sURL is equal to current page URL
      */
    public function _isCurrentUrl($sURL, $sServerHost)
    {
        // #4010: force_sid added in https to every link
        preg_match("/^(https?:\/\/)?(www\.)?([^\/]+)/i", $sURL, $matches);
        $sUrlHost = isset($matches[3]) ? $matches[3] : null;

        preg_match("/^(https?:\/\/)?(www\.)?([^\/]+)/i", $sServerHost, $matches);
        $sRealHost =  isset($matches[3]) ? $matches[3] : null;


        //fetch the path from SCRIPT_NAME and ad it to the $sServerHost
        $sScriptName = $this->getServerVar('SCRIPT_NAME');
        $sCurrentHost = preg_replace('/\/(modules\/[\w\/]*)?\w*\.php.*/', '', $sServerHost . $sScriptName);

        //remove double slashes all the way
        $sCurrentHost = str_replace('/', '', $sCurrentHost);
        $sURL = str_replace('/', '', $sURL);

        if ($sURL && $sCurrentHost && strpos($sURL, $sCurrentHost) !== false) {
            //bug fix #0002991
            if ($sUrlHost == $sRealHost) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return server id by server system information.
     *
     * @return string
     */
    public function getServerNodeId()
    {
        return md5($this->getServerName() . $this->getServerIp());
    }

    /**
     * Return local machine ip.
     *
     * @return string
     */
    public function getServerIp()
    {
        return $this->getServerVar('SERVER_ADDR');
    }

    /**
     * Return server system parameter similar as unix uname.
     *
     * @return string
     */
    private function getServerName()
    {
        return php_uname();
    }
}
