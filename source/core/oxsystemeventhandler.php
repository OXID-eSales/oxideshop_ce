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
            /** @var oxOnlineModuleVersionNotifier $oOnlineModuleVersionNotifier */
            $oOnlineModuleVersionNotifier = oxNew("oxOnlineModuleVersionNotifier");
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
        if($this->_needToSendShopInformation()) {
            $this->_updateInformationSentTimeStamp();
            $oOnlineLicenseCheck = $this->getOnlineLicenseCheck();
            $oOnlineLicenseCheck->validate();
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

        $oConfig = $this->_getConfig();
        $oUtilsDate = $this->_getDate();
        $sOnlineLicenseCheckTime = $oConfig->getConfigParam('sOnlineLicenseCheckTime');
        if (($sOnlineLicenseCheckTime + 25 * 60 * 60) < $oUtilsDate->getTime()) {
            $blNeedToSend = true;
        }

        return $blNeedToSend;
    }

    private function _updateInformationSentTimeStamp()
    {
        $oUtilsDate = $this->_getDate();
        $sOnlineLicenseInvalidTime = $oUtilsDate->getTime();
        $this->_getConfig()->setConfigParam('sOnlineLicenseCheckTime', $sOnlineLicenseInvalidTime);
    }

    /**
     * @return oxConfig
     */
    private function _getConfig()
    {
        return oxRegistry::getConfig();
    }

    /**
     * @return oxUtilsDate
     */
    private function _getDate()
    {
        return oxRegistry::get('oxUtilsDate');
    }
}