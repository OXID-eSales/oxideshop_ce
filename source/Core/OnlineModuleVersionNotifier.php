<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
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
     * @deprecated underscore prefix violates PSR12, will be renamed to "prepareModulesInformation" in next major
     */
    protected function _prepareModulesInformation() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $preparedModules = [];

        $container = ContainerFactory::getInstance()->getContainer();
        $shopConfiguration = $container->get(ShopConfigurationDaoBridgeInterface::class)->get();
        $moduleActivationBridge = $container->get(ModuleActivationBridgeInterface::class);

        foreach ($shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
            /** @var \OxidEsales\Eshop\Core\Module\Module $oModule */

            $preparedModule = new stdClass();
            $preparedModule->id = $moduleConfiguration->getId();
            $preparedModule->version = $moduleConfiguration->getVersion();

            $preparedModule->activeInShops = new stdClass();
            $preparedModule->activeInShops->activeInShop = [];
            if ($moduleActivationBridge->isActive($moduleConfiguration->getId(), Registry::getConfig()->getShopId())) {
                $preparedModule
                    ->activeInShops
                    ->activeInShop[] = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopUrl();
            }
            $preparedModules[] = $preparedModule;
        }

        return $preparedModules;
    }

    /**
     * Send request message to Online Module Version Notifier web service.
     *
     * @return oxOnlineModulesNotifierRequest
     * @deprecated underscore prefix violates PSR12, will be renamed to "formRequest" in next major
     */
    protected function _formRequest() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
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
     * @deprecated underscore prefix violates PSR12, will be renamed to "getOnlineModuleNotifierCaller" in next major
     */
    protected function _getOnlineModuleNotifierCaller() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->_oCaller;
    }

    /**
     * @deprecated since v6.4.0 (09-08-2019). Use ShopConfigurationDaoBridgeInterface
     *
     * Returns shops array of modules.
     *
     * @return array
     */
    protected function _getModules() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $shopConfiguration = $container->get(ShopConfigurationDaoBridgeInterface::class)->get();

        $modules = [];

        foreach ($shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
            $module = oxNew(Module::class);
            $module->load($moduleConfiguration->getId());
            $modules[$moduleConfiguration->getId()] = $module;
        }

        ksort($modules);

        return $modules;
    }
}
