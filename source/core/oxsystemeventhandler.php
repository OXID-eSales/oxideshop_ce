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
 * Contains system event handler methods
 *
 * @internal Do not make a module extension for this class.
 * @see http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class oxSystemEventHandler
{
    /**
     * @Var oxOnlineModuleVersionNotifier
     */
    private $_oOnlineModuleVersionNotifier = null;

    /**
     * @Var oxOnlineLicenseCheck
     */
    private $_oOnlineLicenseCheck = null;

    /**
     * OLC dependency setter
     *
     * @param oxOnlineLicenseCheck $oOnlineLicenseCheck
     */
    public function setOnlineLicenseCheck(oxOnlineLicenseCheck $oOnlineLicenseCheck)
    {
        $this->_oOnlineLicenseCheck = $oOnlineLicenseCheck;
    }

    /**
     * OLC dependency getter
     *
     * @return oxOnlineLicenseCheck
     */
    public function getOnlineLicenseCheck()
    {
        if (!$this->_oOnlineLicenseCheck) {
            /** @var oxOnlineLicenseCheck $oOLC */
            $oOLC = oxNew("oxOnlineLicenseCheck");
            $this->setOnlineLicenseCheck( $oOLC );
        }

        return $this->_oOnlineLicenseCheck;
    }

    /**
     * oxOnlineModuleVersionNotifier dependency setter
     *
     * @param oxOnlineModuleVersionNotifier $oOnlineModuleVersionNotifier
     */
    public function setOnlineModuleVersionNotifier(oxOnlineModuleVersionNotifier $oOnlineModuleVersionNotifier)
    {
        $this->_oOnlineModuleVersionNotifier = $oOnlineModuleVersionNotifier;
    }

    /**
     * oxOnlineModuleVersionNotifier dependency getter
     *
     * @return oxOnlineModuleVersionNotifier
     */
    public function getOnlineModuleVersionNotifier()
    {
        if (!$this->_oOnlineModuleVersionNotifier) {
            /** @var oxCurl $oCurl */
            $oCurl = oxNew('oxCurl');

            /** @var  oxOnlineServerEmailBuilder $oMailBuilder */
            $oMailBuilder = oxNew('oxOnlineServerEmailBuilder');

            /** @var oxOnlineCaller $oOnlineCaller */
            $oOnlineCaller = oxNew('oxOnlineCaller', $oCurl, $oMailBuilder);

            /** @var oxOnlineModuleVersionNotifierCaller $oOnlineModuleVersionNotifierCaller */
            $oOnlineModuleVersionNotifierCaller = oxNew("oxOnlineModuleVersionNotifierCaller", $oOnlineCaller);

            /** @var oxModuleList $oModuleList */
            $oModuleList = oxNew('oxModuleList');
            $oModuleList->getModulesFromDir(oxRegistry::getConfig()->getModulesDir());

            /** @var oxOnlineModuleVersionNotifier $oOnlineModuleVersionNotifier */
            $oOnlineModuleVersionNotifier = oxNew("oxOnlineModuleVersionNotifier", $oOnlineModuleVersionNotifierCaller, $oModuleList);

            $this->setOnlineModuleVersionNotifier( $oOnlineModuleVersionNotifier );
        }

        return $this->_oOnlineModuleVersionNotifier;
    }

    /**
     * onAdminLogin() is called on every successful login to the backend
     *
     * @param string $sActiveShop Active shop
     */
    public function onAdminLogin( $sActiveShop )
    {
        // Checks if newer versions of modules are available.
        // Will be used by the upcoming online one click installer.
        // Is still under development - still changes at the remote server are necessary - therefore ignoring the results for now
        try {
            $this->getOnlineModuleVersionNotifier()->versionNotify();
        } catch (Exception $o) { }
    }

    /**
     * Perform shop startup related actions, like license check.
     */
    public function onShopStart()
    {
        $this->_validateOffline();
        $this->_sendShopInformation();
    }

    protected function _sendShopInformation()
    {
        if ($this->_needToSendShopInformation()) {
            $oOnlineLicenseCheck = $this->getOnlineLicenseCheck();
            $oOnlineLicenseCheck->validate();
            $this->_updateNextCheckTime();
        }
    }

    /**
     * Check if need to send information.
     * We will not send information on each request due to possible performance drop.
     *
     * @return bool
     */
    private function _needToSendShopInformation()
    {
        $blNeedToSend = false;

        if ($this->_getLastCheckTime() < $this->_getCurrentTime()) {
            $blNeedToSend = true;
        }

        return $blNeedToSend;
    }

    /**
     * Return time stamp when shop was checked last with white noise from config.
     *
     * @return int
     */
    private function _getLastCheckTime()
    {
        return (int) $this->_getConfig()->getConfigParam('sOnlineLicenseCheckTime');
    }

    /**
     * Update when shop was checked last time with white noise.
     * White noise is used to separate call time for different shop.
     */
    private function _updateNextCheckTime()
    {
        $this->_getConfig()->saveShopConfVar('arr', 'sOnlineLicenseCheckTime',
            $this->_getCurrentTime() + $this->_getWhiteNoise() + $this->_getLicenseCheckValidityTime());
    }

    /**
     * License check is done after 24 hours from last check.
     *
     * @return int
     */
    private function _getLicenseCheckValidityTime()
    {
        return 24 * 60 * 60;
    }

    /**
     * Get white noise so each license check would be performed in different time.
     *
     * @return int
     */
    private function _getWhiteNoise()
    {
        $iWhiteNoiseMinTime = 0;
        $iWhiteNoiseMaxTime = 12 * 60 * 60;

        return rand($iWhiteNoiseMinTime, $iWhiteNoiseMaxTime);
    }

    /**
     * Return current time - time stamp.
     *
     * @return int
     */
    private function _getCurrentTime()
    {
        $oUtilsDate = oxRegistry::get('oxUtilsDate');
        $iCurrentTime = $oUtilsDate->getTime();

        return $iCurrentTime;
    }

    /**
     * Check if shop valid.
     * Redirect offline if not valid.
     */
    private function _validateOffline()
    {
    }

    /**
     * Performance - run these checks only each 5 times statistically.
     *
     * @return bool
     */
    private function _needValidateShop()
    {
    }

    /**
     * Return oxConfig from registry.
     *
     * @return oxConfig
     */
    protected function _getConfig()
    {
        return oxRegistry::getConfig();
    }
}