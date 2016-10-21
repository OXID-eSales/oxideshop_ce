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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core;

use Exception;
use oxRegistry;
use oxOnlineLicenseCheck;
use oxCurl;
use oxOnlineServerEmailBuilder;
use oxSimpleXml;
use oxOnlineLicenseCheckCaller;
use oxOnlineModuleVersionNotifierCaller;
use oxOnlineModuleVersionNotifier;
use oxModuleList;
use oxUtilsDate;
use oxServersManager;
use oxServerChecker;
use oxUtilsServer;
use oxServerProcessor;

/**
 * Contains system event handler methods
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class SystemEventHandler
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
            /** @var oxCurl $oCurl */
            $oCurl = oxNew('oxCurl');

            /** @var oxOnlineServerEmailBuilder $oEmailBuilder */
            $oEmailBuilder = oxNew('oxOnlineServerEmailBuilder');

            /** @var oxSimpleXml $oSimpleXml */
            $oSimpleXml = oxNew('oxSimpleXml');

            /** @var oxOnlineLicenseCheckCaller $oLicenseCaller */
            $oLicenseCaller = oxNew('oxOnlineLicenseCheckCaller', $oCurl, $oEmailBuilder, $oSimpleXml);

            /** @var oxUserCounter $oUserCounter */
            $oUserCounter = oxNew('oxUserCounter');

            /** @var oxServersManager $oServerManager */
            $oServerManager = oxNew('oxServersManager');

            /** @var oxOnlineLicenseCheck $oOLC */
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

            /** @var oxSimpleXml $oSimpleXml */
            $oSimpleXml = oxNew('oxSimpleXml');

            /** @var oxOnlineModuleVersionNotifierCaller $oOnlineModuleVersionNotifierCaller */
            $oOnlineModuleVersionNotifierCaller = oxNew("oxOnlineModuleVersionNotifierCaller", $oCurl, $oMailBuilder, $oSimpleXml);

            /** @var oxModuleList $oModuleList */
            $oModuleList = oxNew('oxModuleList');
            $oModuleList->getModulesFromDir(oxRegistry::getConfig()->getModulesDir());

            /** @var oxOnlineModuleVersionNotifier $oOnlineModuleVersionNotifier */
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

        if ($this->_isSendingShopDataEnabled() && !oxRegistry::getUtils()->isSearchEngine()) {
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

        /** @var oxUtilsDate $oUtilsDate */
        $oUtilsDate = oxRegistry::get('oxUtilsDate');
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
        /** @var oxUtilsDate $oUtilsDate */
        $oUtilsDate = oxRegistry::get('oxUtilsDate');

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
     * @return oxConfig
     */
    protected function _getConfig()
    {
        return oxRegistry::getConfig();
    }

    /**
     * Gets server processor.
     *
     * @return oxServerProcessor
     */
    protected function _getServerProcessor()
    {
        /** @var oxServersManager $oServerNodesManager */
        $oServerNodesManager = oxNew('oxServersManager');

        /** @var oxServerChecker $oServerNodeChecker */
        $oServerNodeChecker = oxNew('oxServerChecker');

        /** @var oxUtilsServer $oUtilsServer */
        $oUtilsServer = oxNew('oxUtilsServer');

        /** @var oxUtilsDate $oUtilsDate */
        $oUtilsDate = oxRegistry::get('oxUtilsDate');

        /** @var oxServerProcessor $oProcessor */

        return oxNew('oxServerProcessor', $oServerNodesManager, $oServerNodeChecker, $oUtilsServer, $oUtilsDate);
    }
}
