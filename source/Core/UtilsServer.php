<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Str;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

use function array_key_exists;
use function in_array;

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
     * @param string $sName cookie name
     * @param string $sValue value
     * @param int $iExpire expire time
     * @param string $sPath The path on the server in which the cookie will be available on
     * @param string $sDomain The domain that the cookie is available.
     * @param bool $blToSession is true, records cookie information to session
     * @param bool $blSecure if true, transfer cookie only via SSL
     * @param bool $blHttpOnly if true, only accessible via HTTP
     *
     * @return bool
     */
    public function setOxCookie($sName, $sValue = "", $iExpire = 0, $sPath = '/', $sDomain = null, $blToSession = true, $blSecure = false, $blHttpOnly = true)
    {
        if ($blToSession && !$this->isAdmin()) {
            $this->saveSessionCookie($sName, $sValue, $iExpire, $sPath, $sDomain);
        }

        if (php_sapi_name() === 'cli') {
            // do NOT set cookies in cli because it would issue warnings
            return;
        }

        //if shop runs in https only mode we can set secure flag to all cookies
        $blSecure = $blSecure || Registry::getConfig()->isSsl();
        return setcookie(
            $sName,
            $sValue,
            $iExpire,
            $this->getCookiePath($sPath),
            $this->getCookieDomain($sDomain),
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
    protected function mustSaveToSession()
    {
        if ($this->_blSaveToSession === null) {
            $this->_blSaveToSession = false;

            $myConfig = Registry::getConfig();
            if ($myConfig->getShopUrl()) {
                return true;
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
    protected function getSessionCookieKey($blGet)
    {
        $blSsl = Registry::getConfig()->isSsl();
        $sKey = $blSsl ? 'nossl' : 'ssl';

        if ($blGet) {
            $sKey = $blSsl ? 'ssl' : 'nossl';
        }

        return $sKey;
    }

    /**
     * Copies cookie info to session
     *
     * @param string $sName cookie name
     * @param string $sValue cookie value
     * @param int $iExpire expiration time
     * @param string $sPath cookie path
     * @param string $sDomain cookie domain
     */
    protected function saveSessionCookie($sName, $sValue, $iExpire, $sPath, $sDomain)
    {
        if ($this->mustSaveToSession()) {
            $aCookieData = ['value' => $sValue, 'expire' => $iExpire, 'path' => $sPath, 'domain' => $sDomain];

            $aSessionCookies = (array)\OxidEsales\Eshop\Core\Registry::getSession()->getVariable($this->_sSessionCookiesName);
            $aSessionCookies[$this->getSessionCookieKey(false)][$sName] = $aCookieData;

            Registry::getSession()->setVariable($this->_sSessionCookiesName, $aSessionCookies);
        }
    }

    /**
     * Stored all session cookie info to cookies
     */
    public function loadSessionCookies()
    {
        if (($aSessionCookies = Registry::getSession()->getVariable($this->_sSessionCookiesName))) {
            $sKey = $this->getSessionCookieKey(true);
            if (isset($aSessionCookies[$sKey])) {
                // writing session data to cookies
                foreach ($aSessionCookies[$sKey] as $sName => $aCookieData) {
                    $this->setOxCookie($sName, $aCookieData['value'], $aCookieData['expire'], $aCookieData['path'], $aCookieData['domain'], false);
                    $this->_sSessionCookies[$sName] = $aCookieData['value'];
                }

                // cleanup
                unset($aSessionCookies[$sKey]);
                Registry::getSession()->setVariable($this->_sSessionCookiesName, $aSessionCookies);
            }
        }
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getCookiePath($path)
    {
        return ContainerFacade::getParameter(
            'oxid_cookie_paths'
        )[ContainerFacade::get(ContextInterface::class)->getCurrentShopId()] ??
            $path ?:
            '';
    }

    /**
     * @param string $domain
     *
     * @return string
     */
    protected function getCookieDomain($domain)
    {
        return $domain ?:
            ContainerFacade::getParameter(
                'oxid_cookie_domains'
            )[ContainerFacade::get(ContextInterface::class)->getCurrentShopId()] ??
            '';
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
            $sValue = Registry::getConfig()->checkParamSpecialChars($_COOKIE[$sName]);
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
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $sIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $sIP = preg_replace('/,.*$/', '', $sIP);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $sIP = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $sIP = $_SERVER['REMOTE_ADDR'] ?? null;
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

    public function setUserCookie($userName, $passwordHash, $shopId = null, $timeout = 31536000, $salt = User::USER_COOKIE_SALT)
    {
        $myConfig = Registry::getConfig();
        $shopId = $shopId ?? $myConfig->getShopId();
        $sslUrl = $myConfig->getShopUrl();
        if (stripos($sslUrl, 'https') === 0) {
            $ssl = true;
        } else {
            $ssl = false;
        }

        $passwordServiceBridge = ContainerFacade::get(PasswordServiceBridgeInterface::class);

        $this->_aUserCookie[$shopId] = $userName . '@@@' .  $passwordServiceBridge->hash($passwordHash . $salt);
        $this->setOxCookie('oxid_' . $shopId, $this->_aUserCookie[$shopId], Registry::getUtilsDate()->getTime() + $timeout, '/', null, true, $ssl);
        $this->setOxCookie('oxid_' . $shopId . '_autologin', '1', Registry::getUtilsDate()->getTime() + $timeout, '/', null, true, false);
    }

    public function deleteUserCookie($shopId = null)
    {
        $myConfig = Registry::getConfig();
        $shopId = (!$shopId) ? Registry::getConfig()->getShopId() : $shopId;
        $sslUrl = $myConfig->getShopUrl();
        if (stripos($sslUrl, 'https') === 0) {
            $ssl = true;
        } else {
            $ssl = false;
        }

        $this->_aUserCookie[$shopId] = '';
        $this->setOxCookie('oxid_' . $shopId, '', Registry::getUtilsDate()->getTime() - 3600, '/', null, true, $ssl);
        $this->setOxCookie('oxid_' . $shopId . '_autologin', '0', Registry::getUtilsDate()->getTime() - 3600, '/', null, true, false);
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
        $myConfig = Registry::getConfig();
        $sShopId = (!$sShopId) ? $myConfig->getShopId() : $sShopId;
        // check for SSL connection
        if (!$myConfig->isSsl() && $this->getOxCookie('oxid_' . $sShopId . '_autologin') == '1') {
            $sslUrl = rtrim($myConfig->getShopUrl(), '/') . $_SERVER['REQUEST_URI'];
            if (stripos($sslUrl, 'https') === 0) {
                Registry::getUtils()->redirect($sslUrl, true, 302);
            }
        }

        if (array_key_exists($sShopId, $this->_aUserCookie) && $this->_aUserCookie[$sShopId] !== null) {
            return $this->_aUserCookie[$sShopId] ?: null;
        }

        return $this->_aUserCookie[$sShopId] = $this->getOxCookie('oxid_' . $sShopId);
    }

    /**
     * @return bool
     */
    public function isTrustedClientIp()
    {
        return in_array(
            $this->getRemoteAddress(),
            ContainerFacade::getParameter('oxid_trusted_ips'),
            true
        );
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
            $sAgent = Str::getStr()->preg_replace("/MSIE(\s)?(\S)*(\s)/", "", (string)$sAgent);
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
        $blIsCurrentUrl = $this->isUrlHostServerHost($sURL, $sServerHost);
        if (!$blIsCurrentUrl) {
            $sServerHost = $this->getServerVar('HTTP_X_FORWARDED_HOST');
            if ($sServerHost) {
                $blIsCurrentUrl = $this->isUrlHostServerHost($sURL, $sServerHost);
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
     * @param string $sURL URL to check if is same as request.
     * @param string $sServerHost request host.
     *
     * @return bool true if $sURL is equal to current page URL
     */
    public function isUrlHostServerHost($sURL, $sServerHost): bool
    {
        // #4010: force_sid added in https to every link
        preg_match("/^(https?:\/\/)?(www\.)?([^\/]+)/i", $sURL, $matches);
        $sUrlHost = $matches[3] ?? null;

        preg_match("/^(https?:\/\/)?(www\.)?([^\/]+)/i", (string)$sServerHost, $matches);
        $sRealHost = $matches[3] ?? null;


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
