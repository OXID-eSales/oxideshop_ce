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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core;

use Exception;

/**
 * Contains system event handler methods
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class SystemEventHandler
{

    /**
     * @var \OxidEsales\Eshop\Core\OnlineModuleVersionNotifier
     */
    private $_oOnlineModuleVersionNotifier = null;

    /**
     * @var \OxidEsales\Eshop\Core\OnlineLicenseCheck
     */
    private $_oOnlineLicenseCheck = null;

    /**
     * OLC dependency setter
     *
     * @param \OxidEsales\Eshop\Core\OnlineLicenseCheck $oOnlineLicenseCheck
     */
    public function setOnlineLicenseCheck(\OxidEsales\Eshop\Core\OnlineLicenseCheck $oOnlineLicenseCheck)
    {
        $this->_oOnlineLicenseCheck = $oOnlineLicenseCheck;
    }

    /**
     * OLC dependency getter
     *
     * @return \OxidEsales\Eshop\Core\OnlineLicenseCheck
     */
    public function getOnlineLicenseCheck()
    {
        if (!$this->_oOnlineLicenseCheck) {
            /** @var \OxidEsales\Eshop\Core\Curl $oCurl */
            $oCurl = oxNew(\OxidEsales\Eshop\Core\Curl::class);

            /** @var \OxidEsales\Eshop\Core\OnlineServerEmailBuilder $oEmailBuilder */
            $oEmailBuilder = oxNew(\OxidEsales\Eshop\Core\OnlineServerEmailBuilder::class);

            /** @var \OxidEsales\Eshop\Core\SimpleXml $oSimpleXml */
            $oSimpleXml = oxNew(\OxidEsales\Eshop\Core\SimpleXml::class);

            /** @var \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller $oLicenseCaller */
            $oLicenseCaller = oxNew('oxOnlineLicenseCheckCaller', $oCurl, $oEmailBuilder, $oSimpleXml);

            /** @var \OxidEsales\Eshop\Core\UserCounter $oUserCounter */
            $oUserCounter = oxNew(\OxidEsales\Eshop\Core\UserCounter::class);

            /** @var \OxidEsales\Eshop\Core\ServersManager $oServerManager */
            $oServerManager = oxNew(\OxidEsales\Eshop\Core\ServersManager::class);

            /** @var \OxidEsales\Eshop\Core\OnlineLicenseCheck $oOLC */
            $oOLC = oxNew("oxOnlineLicenseCheck", $oLicenseCaller);
            $oOLC->setServersManager($oServerManager);
            $oOLC->setUserCounter($oUserCounter);

            $this->setOnlineLicenseCheck($oOLC);
        }

        return $this->_oOnlineLicenseCheck;
    }

    /**
     * oxOnlineModuleVersionNotifier dependency setter
     *
     * @param \OxidEsales\Eshop\Core\OnlineModuleVersionNotifier $oOnlineModuleVersionNotifier
     */
    public function setOnlineModuleVersionNotifier(\OxidEsales\Eshop\Core\OnlineModuleVersionNotifier $oOnlineModuleVersionNotifier)
    {
        $this->_oOnlineModuleVersionNotifier = $oOnlineModuleVersionNotifier;
    }

    /**
     * oxOnlineModuleVersionNotifier dependency getter
     *
     * @return \OxidEsales\Eshop\Core\OnlineModuleVersionNotifier
     */
    public function getOnlineModuleVersionNotifier()
    {
        if (!$this->_oOnlineModuleVersionNotifier) {
            /** @var \OxidEsales\Eshop\Core\Curl $oCurl */
            $oCurl = oxNew(\OxidEsales\Eshop\Core\Curl::class);

            /** @var \OxidEsales\Eshop\Core\OnlineServerEmailBuilder $oMailBuilder */
            $oMailBuilder = oxNew(\OxidEsales\Eshop\Core\OnlineServerEmailBuilder::class);

            /** @var \OxidEsales\Eshop\Core\SimpleXml $oSimpleXml */
            $oSimpleXml = oxNew(\OxidEsales\Eshop\Core\SimpleXml::class);

            /** @var \OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller $oOnlineModuleVersionNotifierCaller */
            $oOnlineModuleVersionNotifierCaller = oxNew("oxOnlineModuleVersionNotifierCaller", $oCurl, $oMailBuilder, $oSimpleXml);

            /** @var \OxidEsales\Eshop\Core\Module\ModuleList $oModuleList */
            $oModuleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
            $oModuleList->getModulesFromDir(\OxidEsales\Eshop\Core\Registry::getConfig()->getModulesDir());

            /** @var \OxidEsales\Eshop\Core\OnlineModuleVersionNotifier $oOnlineModuleVersionNotifier */
            $oOnlineModuleVersionNotifier = oxNew("oxOnlineModuleVersionNotifier", $oOnlineModuleVersionNotifierCaller, $oModuleList);

            $this->setOnlineModuleVersionNotifier($oOnlineModuleVersionNotifier);
        }

        return $this->_oOnlineModuleVersionNotifier;
    }

    /**
     * onAdminLogin() is called on every successful login to the backend
     */
    public function onAdminLogin()
    {
        // Checks if newer versions of modules are available.
        // Will be used by the upcoming online one click installer.
        // Is still under development - still changes at the remote server are necessary - therefore ignoring the results for now
        try {
            $this->getOnlineModuleVersionNotifier()->versionNotify();
        } catch (Exception $o) {
        }
    }

    /**
     * Perform shop startup related actions, like license check.
     */
    public function onShopStart()
    {
        $oProcessor = $this->_getServerProcessor();
        $oProcessor->process();

        if ($this->_isSendingShopDataEnabled() && !\OxidEsales\Eshop\Core\Registry::getUtils()->isSearchEngine()) {
            $this->_sendShopInformation();
        }

        $this->_validateOffline();
    }

    /**
     * Checks if sending shop data is enabled.
     *
     * @return bool
     */
    protected function _isSendingShopDataEnabled()
    {
        return (bool) $this->_getConfig()->getConfigParam('blLoadDynContents');
    }

    /**
     * Sends shop information to oxid servers.
     */
    protected function _sendShopInformation()
    {
        if ($this->_needToSendShopInformation()) {
            $oOnlineLicenseCheck = $this->getOnlineLicenseCheck();
            $oOnlineLicenseCheck->validateShopSerials();
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
        return $this->_getNextCheckTime() < $this->_getCurrentTime();
    }

    /**
     * Return time stamp when shop was checked last with white noise from config.
     *
     * @return int
     */
    private function _getNextCheckTime()
    {
        return (int) $this->_getConfig()->getSystemConfigParameter('sOnlineLicenseNextCheckTime');
    }

    /**
     * Update when shop was checked last time with white noise.
     * White noise is used to separate call time for different shop.
     */
    private function _updateNextCheckTime()
    {
        $sHourToCheck = $this->_getCheckTime();

        /** @var \OxidEsales\Eshop\Core\UtilsDate $oUtilsDate */
        $oUtilsDate = \OxidEsales\Eshop\Core\Registry::getUtilsDate();
        $iNextCheckTime = $oUtilsDate->formTime('tomorrow', $sHourToCheck);

        $this->_getConfig()->saveSystemConfigParameter('str', 'sOnlineLicenseNextCheckTime', $iNextCheckTime);
    }

    /**
     * Returns time (hour minutes seconds) when to perform license check.
     * Create if does not exist.
     *
     * @return string time formed as H:i:s
     */
    private function _getCheckTime()
    {
        $sCheckTime = $this->_getConfig()->getSystemConfigParameter('sOnlineLicenseCheckTime');
        if (!$sCheckTime) {
            $iHourToCheck = rand(8, 23);
            $iMinuteToCheck = rand(0, 59);
            $iSecondToCheck = rand(0, 59);

            $sCheckTime = $iHourToCheck . ':' . $iMinuteToCheck . ':' . $iSecondToCheck;
            $this->_getConfig()->saveSystemConfigParameter('str', 'sOnlineLicenseCheckTime', $sCheckTime);
        }

        return $sCheckTime;
    }

    /**
     * Return current time - time stamp.
     *
     * @return int
     */
    private function _getCurrentTime()
    {
        /** @var \OxidEsales\Eshop\Core\UtilsDate $oUtilsDate */
        $oUtilsDate = \OxidEsales\Eshop\Core\Registry::getUtilsDate();

        return $oUtilsDate->getTime();
    }

    /**
     * Check if shop valid and do related actions.
     */
    protected function _validateOffline()
    {
    }

    /**
     * Return oxConfig from registry.
     *
     * @return \OxidEsales\Eshop\Core\Config
     */
    protected function _getConfig()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig();
    }

    /**
     * Gets server processor.
     *
     * @return \OxidEsales\Eshop\Core\ServerProcessor
     */
    protected function _getServerProcessor()
    {
        /** @var \OxidEsales\Eshop\Core\ServersManager $oServerNodesManager */
        $oServerNodesManager = oxNew(\OxidEsales\Eshop\Core\ServersManager::class);

        /** @var \OxidEsales\Eshop\Core\ServerChecker $oServerNodeChecker */
        $oServerNodeChecker = oxNew(\OxidEsales\Eshop\Core\ServerChecker::class);

        /** @var \OxidEsales\Eshop\Core\UtilsServer $oUtilsServer */
        $oUtilsServer = oxNew(\OxidEsales\Eshop\Core\UtilsServer::class);

        /** @var \OxidEsales\Eshop\Core\UtilsDate $oUtilsDate */
        $oUtilsDate = \OxidEsales\Eshop\Core\Registry::getUtilsDate();

        /** @var \OxidEsales\Eshop\Core\ServerProcessor $oProcessor */

        return oxNew('oxServerProcessor', $oServerNodesManager, $oServerNodeChecker, $oUtilsServer, $oUtilsDate);
    }
}
