<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup;

/**
 * Setup session manager class
 */
class Session extends Core
{
    /**
     * Session data array
     *
     * @var array
     */
    protected $_aSessionData = null;

    /**
     * Session ID
     *
     * @var string
     */
    protected $_sSid = null;

    /**
     * Session name
     *
     * @var string
     */
    protected $_sSessionName = 'setup_sid';

    /**
     * Is new session
     *
     * @var bool
     */
    protected $_blNewSession = false;

    /**
     * Initialize session class
     */
    public function __construct()
    {
        ini_set('session.use_cookies', 0);

        // initialize session
        $this->_startSession();
        $this->_initSessionData();
    }

    /**
     * Start session
     */
    protected function _startSession()
    {
        session_name($this->_sSessionName);

        /** @var Utilities $oUtils */
        $oUtils = $this->getInstance("Utilities");
        $sSid = $oUtils->getRequestVar('sid', 'get');

        if (empty($sSid)) {
            $sSid = $oUtils->getRequestVar('sid', 'post');
        }

        if (!empty($sSid)) {
            session_id($sSid);
        }

        session_start();
        $sSid = $this->_validateSession();
        $this->setSid($sSid);
    }

    /**
     * Validate if session is started by setup script, if not, generate new session.
     *
     * @return string Session ID
     */
    protected function _validateSession()
    {
        if ($this->getIsNewSession() === true) {
            $this->setSessionParam('setup_session', true);
        } elseif ($this->getSessionParam('setup_session') !== true) {
            $sNewSid = $this->_getNewSessionID();
            session_write_close();

            session_id($sNewSid);
            session_start();
            $this->setSessionParam('setup_session', true);
        }

        return session_id();
    }

    /**
     * Generate new unique session ID
     *
     * @return string
     */
    protected function _getNewSessionID()
    {
        session_regenerate_id(false);
        $this->setIsNewSession(true);

        return session_id();
    }

    /**
     * Returns session id, which is used in forms and urls
     * (actually this id keeps all session data)
     *
     * @return string
     */
    public function getSid()
    {
        return $this->_sSid;
    }

    /**
     * Sets current session ID
     *
     * @param string $sSid session ID
     */
    public function setSid($sSid)
    {
        $this->_sSid = $sSid;
    }

    /**
     * Initializes setup session data array
     */
    protected function _initSessionData()
    {
        /** @var Utilities $oUtils */
        $oUtils = $this->getInstance("Utilities");

        //storing country value settings to session
        $sCountryLang = $oUtils->getRequestVar("country_lang", "post");
        if (isset($sCountryLang)) {
            $this->setSessionParam('country_lang', $sCountryLang);
        }

        //storing shop language value settings to session
        $sShopLang = $oUtils->getRequestVar("sShopLang", "post");
        if (isset($sShopLang)) {
            $this->setSessionParam('sShopLang', $sShopLang);
        }

        //storing if send information to OXID
        $blSendInformation = $oUtils->getRequestVar("send_technical_information_to_oxid", "post");
        if (isset($blSendInformation)) {
            $this->setSessionParam('send_technical_information_to_oxid', $blSendInformation);
        }

        //storing check for updates settings to session
        $blCheckForUpdates = $oUtils->getRequestVar("check_for_updates", "post");
        if (isset($blCheckForUpdates)) {
            $this->setSessionParam('check_for_updates', $blCheckForUpdates);
        }

        // store eula to session
        $iEula = $oUtils->getRequestVar("iEula", "post");
        if (isset($iEula)) {
            $this->setSessionParam('eula', $iEula);
        }
    }

    /**
     * Return session object reference.
     *
     * @return array
     */
    protected function &_getSessionData()
    {
        return $_SESSION;
    }

    /**
     * @param bool $value
     */
    public function setIsNewSession($value)
    {
        $this->_blNewSession = $value;
    }

    /**
     * @return bool
     */
    public function getIsNewSession()
    {
        return $this->_blNewSession;
    }

    /**
     * Returns session parameter value
     *
     * @param string $sParamName parameter name
     *
     * @return mixed
     */
    public function getSessionParam($sParamName)
    {
        $aSessionData = & $this->_getSessionData();
        if (isset($aSessionData[$sParamName])) {
            return $aSessionData[$sParamName];
        }
    }

    /**
     * Sets session parameter value
     *
     * @param string $sParamName  parameter name
     * @param mixed  $sParamValue parameter value
     */
    public function setSessionParam($sParamName, $sParamValue)
    {
        $aSessionData = & $this->_getSessionData();
        $aSessionData[$sParamName] = $sParamValue;
    }
}
