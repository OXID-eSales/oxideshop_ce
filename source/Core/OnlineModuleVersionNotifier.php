<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use stdClass;
use oxOnlineModulesNotifierRequest;

/**
 * Performs Online Module Version Notifier check.
 *
 * The Online Module Version Notification is used for checking if newer versions of modules are available.
 * Will be used by the upcoming online one click installer.
 * Is still under development - still changes at the remote server are necessary - therefore ignoring the results for now
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 */
class OnlineModuleVersionNotifier
{
    /** @var \OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller */
    private $_oCaller;

    /**
     * Class constructor, initiates class parameters.
     *
     * @param \OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller $oCaller Online module version notifier caller object
     */
    public function __construct(\OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller $oCaller)
    {
        $this->_oCaller = $oCaller;
    }

    /**
     * Perform Online Module version Notification. Returns result
     *
     * @return null
     */
    public function versionNotify()
    {
        if (true === \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('preventModuleVersionNotify')) {
            return;
        }

        $oOMNCaller = $this->_getOnlineModuleNotifierCaller();
        $oOMNCaller->doRequest($this->_formRequest());
    }

    /**
     * Collects only required modules information and returns as array.
     *
     * @return null
     */
    protected function _prepareModulesInformation()
    {
        $aPreparedModules = [];
        $aModules = $this->_getModules();
        foreach ($aModules as $oModule) {
            /** @var \OxidEsales\Eshop\Core\Module\Module $oModule */

            $oPreparedModule = new stdClass();
            $oPreparedModule->id = $oModule->getId();
            $oPreparedModule->version = $oModule->getInfo('version');

            $oPreparedModule->activeInShops = new stdClass();
            $oPreparedModule->activeInShops->activeInShop = [];
            if ($oModule->isActive()) {
                $oPreparedModule->activeInShops->activeInShop[] = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopUrl();
            }
            $aPreparedModules[] = $oPreparedModule;
        }

        return $aPreparedModules;
    }

    /**
     * Send request message to Online Module Version Notifier web service.
     *
     * @return oxOnlineModulesNotifierRequest
     */
    protected function _formRequest()
    {
        $oRequestParams = new \OxidEsales\Eshop\Core\OnlineModulesNotifierRequest();

        $oRequestParams->modules = new stdClass();
        $oRequestParams->modules->module = $this->_prepareModulesInformation();

        return $oRequestParams;
    }

    /**
     * Returns caller.
     *
     * @return \OxidEsales\Eshop\Core\OnlineModuleVersionNotifierCaller
     */
    protected function _getOnlineModuleNotifierCaller()
    {
        return $this->_oCaller;
    }

    /**
     * Returns shops array of modules.
     *
     * @return array
     */
    protected function _getModules()
    {
        $aModules = $this->getInstalledModules();
        ksort($aModules);

        return $aModules;
    }

    /**
     * @return array
     */
    private function getInstalledModules(): array
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $shopConfiguration = $container->get(ShopConfigurationDaoBridgeInterface::class)->get();

        $modules = [];

        foreach ($shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
            $module = oxNew(Module::class);
            $module->load($moduleConfiguration->getId());
            $modules[$moduleConfiguration->getId()] = $module;
        }

        return $modules;
    }
}
