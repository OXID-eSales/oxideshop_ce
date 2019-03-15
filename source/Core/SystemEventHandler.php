<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use Exception;

/**
 * Contains system event handler methods
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class SystemEventHandler
{
    /**
     * @var \OxidEsales\Eshop\Core\OnlineModuleVersionNotifier
     */
    private $onlineModuleVersionNotifier = null;

    /**
     * @var \OxidEsales\Eshop\Core\OnlineLicenseCheck
     */
    private $onlineLicenseCheck = null;

    /**
     * OLC dependency setter
     *
     * @param \OxidEsales\Eshop\Core\OnlineLicenseCheck $onlineLicenseCheck
     */
    public function setOnlineLicenseCheck(\OxidEsales\Eshop\Core\OnlineLicenseCheck $onlineLicenseCheck)
    {
        $this->onlineLicenseCheck = $onlineLicenseCheck;
    }

    /**
     * OLC dependency getter
     *
     * @return \OxidEsales\Eshop\Core\OnlineLicenseCheck
     */
    public function getOnlineLicenseCheck()
    {
        if (!$this->onlineLicenseCheck) {
            /** @var \OxidEsales\Eshop\Core\Curl $curl */
            $curl = oxNew(\OxidEsales\Eshop\Core\Curl::class);

            /** @var \OxidEsales\Eshop\Core\OnlineServerEmailBuilder $emailBuilder */
            $emailBuilder = oxNew(\OxidEsales\Eshop\Core\OnlineServerEmailBuilder::class);

            /** @var \OxidEsales\Eshop\Core\SimpleXml $simpleXml */
            $simpleXml = oxNew(\OxidEsales\Eshop\Core\SimpleXml::class);

            /** @var \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller $licenseCaller */
            $licenseCaller = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheckCaller::class, $curl, $emailBuilder, $simpleXml);

            /** @var \OxidEsales\Eshop\Core\UserCounter $userCounter */
            $userCounter = oxNew(\OxidEsales\Eshop\Core\UserCounter::class);

            /** @var \OxidEsales\Eshop\Core\Service\ApplicationServerExporter $appServerExporter */
            $appServerExporter = $this->getApplicationServerExporter();

            /** @var \OxidEsales\Eshop\Core\OnlineLicenseCheck $OLC */
            $OLC = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, $licenseCaller);
            $OLC->setAppServerExporter($appServerExporter);
            $OLC->setUserCounter($userCounter);

            $this->setOnlineLicenseCheck($OLC);
        }

        return $this->onlineLicenseCheck;
    }

    /**
     * ApplicationServerExporter dependency setter
     *
     * @return \OxidEsales\Eshop\Core\Service\ApplicationServerExporterInterface
     */
    protected function getApplicationServerExporter()
    {
        $appServerService = $this->getAppServerService();
        return oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerExporter::class, $appServerService);
    }

    /**
     * OnlineModuleVersionNotifier dependency setter
     *
     * @param \OxidEsales\Eshop\Core\OnlineModuleVersionNotifier $onlineModuleVersionNotifier
     */
    public function setOnlineModuleVersionNotifier(\OxidEsales\Eshop\Core\OnlineModuleVersionNotifier $onlineModuleVersionNotifier)
    {
        $this->onlineModuleVersionNotifier = $onlineModuleVersionNotifier;
    }

    /**
     * OnlineModuleVersionNotifier dependency getter
     *
     * @return \OxidEsales\Eshop\Core\OnlineModuleVersionNotifier
     */
    public function getOnlineModuleVersionNotifier()
    {
        if (!$this->onlineModuleVersionNotifier) {
            /** @var \OxidEsales\Eshop\Core\Curl $curl */
            $curl = oxNew(\OxidEsales\Eshop\Core\Curl::class);

            /** @var \OxidEsales\Eshop\Core\OnlineServerEmailBuilder $mailBuilder */
            $mailBuilder = oxNew(\OxidEsales\Eshop\Core\OnlineServerEmailBuilder::class);

            /** @var \OxidEsales\Eshop\Core\SimpleXml $simpleXml */
            $simpleXml = oxNew(\OxidEsales\Eshop\Core\SimpleXml::class);

            /** @var \OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller $onlineModuleVersionNotifierCaller */
            $onlineModuleVersionNotifierCaller = oxNew(
                \OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller::class,
                $curl,
                $mailBuilder,
                $simpleXml
            );

            /** @var \OxidEsales\Eshop\Core\OnlineModuleVersionNotifier $onlineModuleVersionNotifier */
            $onlineModuleVersionNotifier = oxNew(
                \OxidEsales\Eshop\Core\OnlineModuleVersionNotifier::class,
                $onlineModuleVersionNotifierCaller,
                oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class)
            );

            $this->setOnlineModuleVersionNotifier($onlineModuleVersionNotifier);
        }

        return $this->onlineModuleVersionNotifier;
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
            if ($this->isSendingShopDataEnabled()) {
                $this->getOnlineModuleVersionNotifier()->versionNotify();
            }
        } catch (Exception $o) {
        }
    }

    /**
     * Perform shop startup related actions, like license check.
     */
    public function onShopStart()
    {
        $this->validateOffline();
    }

    /**
     * Perform shop finishing up related actions, like updating app server data.
     */
    public function onShopEnd()
    {
        $this->validateOnline();
    }

    /**
     * Check if shop is valid online.
     */
    protected function validateOnline()
    {
        try {
            $appServerService = $this->getAppServerService();
            if ($this->getConfig()->isAdmin()) {
                $appServerService->updateAppServerInformationInAdmin();
            } else {
                $appServerService->updateAppServerInformationInFrontend();
            }

            if ($this->isSendingShopDataEnabled() && !\OxidEsales\Eshop\Core\Registry::getUtils()->isSearchEngine()) {
                $this->sendShopInformation();
            }
        } catch (Exception $exception) {
            \OxidEsales\Eshop\Core\Registry::getLogger()->error($exception->getMessage(), [$exception]);
        }
    }

    /**
     * Checks if sending shop data is enabled.
     *
     * @return bool
     */
    protected function isSendingShopDataEnabled()
    {
        return (bool) $this->getConfig()->getConfigParam('blSendTechnicalInformationToOxid');
    }

    /**
     * Sends shop information to oxid servers.
     */
    protected function sendShopInformation()
    {
        if ($this->needToSendShopInformation()) {
            $onlineLicenseCheck = $this->getOnlineLicenseCheck();
            $onlineLicenseCheck->validateShopSerials();
            $this->updateNextCheckTime();
        }
    }

    /**
     * Check if need to send information.
     * We will not send information on each request due to possible performance drop.
     *
     * @return bool
     */
    private function needToSendShopInformation()
    {
        return $this->getNextCheckTime() < $this->getCurrentTime();
    }

    /**
     * Return time stamp when shop was checked last with white noise from config.
     *
     * @return int
     */
    private function getNextCheckTime()
    {
        return (int) $this->getConfig()->getSystemConfigParameter('sOnlineLicenseNextCheckTime');
    }

    /**
     * Update when shop was checked last time with white noise.
     * White noise is used to separate call time for different shop.
     */
    private function updateNextCheckTime()
    {
        $hourToCheck = $this->getCheckTime();

        /** @var \OxidEsales\Eshop\Core\UtilsDate $utilsDate */
        $utilsDate = \OxidEsales\Eshop\Core\Registry::getUtilsDate();
        $nextCheckTime = $utilsDate->formTime('tomorrow', $hourToCheck);

        $this->getConfig()->saveSystemConfigParameter('str', 'sOnlineLicenseNextCheckTime', $nextCheckTime);
    }

    /**
     * Returns time (hour minutes seconds) when to perform license check.
     * Create if does not exist.
     *
     * @return string time formed as H:i:s
     */
    private function getCheckTime()
    {
        $checkTime = $this->getConfig()->getSystemConfigParameter('sOnlineLicenseCheckTime');
        if (!$checkTime) {
            $hourToCheck = rand(8, 23);
            $minuteToCheck = rand(0, 59);
            $secondToCheck = rand(0, 59);

            $checkTime = $hourToCheck . ':' . $minuteToCheck . ':' . $secondToCheck;
            $this->getConfig()->saveSystemConfigParameter('str', 'sOnlineLicenseCheckTime', $checkTime);
        }

        return $checkTime;
    }

    /**
     * Return current time - time stamp.
     *
     * @return int
     */
    private function getCurrentTime()
    {
        /** @var \OxidEsales\Eshop\Core\UtilsDate $utilsDate */
        $utilsDate = \OxidEsales\Eshop\Core\Registry::getUtilsDate();

        return $utilsDate->getTime();
    }

    /**
     * Check if shop valid and do related actions.
     */
    protected function validateOffline()
    {
    }

    /**
     * Return Config from registry.
     *
     * @return \OxidEsales\Eshop\Core\Config
     */
    protected function getConfig()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig();
    }

    /**
     * Gets application server service.
     *
     * @return \OxidEsales\Eshop\Core\Service\ApplicationServerServiceInterface
     */
    protected function getAppServerService()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $database, $config);
        $utilsServer = oxNew(\OxidEsales\Eshop\Core\UtilsServer::class);

        $appServerService = oxNew(
            \OxidEsales\Eshop\Core\Service\ApplicationServerService::class,
            $appServerDao,
            $utilsServer,
            \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime()
        );

        return $appServerService;
    }
}
