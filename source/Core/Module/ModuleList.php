<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;

/**
 * Modules list class.
 *
 * @deprecated since v6.4.0 (2019-03-22); Use service 'OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface'.
 * @internal Do not make a module extension for this class.
 */
class ModuleList extends \OxidEsales\Eshop\Core\Base
{
    /**
     * All module extensions.
     *
     * @var array
     */
    protected $_aModuleExtensions = null;

    /**
     * @return array
     */
    public function getDisabledModuleClasses()
    {
        $disabledModules = $this->getDisabledModuleConfigurations();
        $disabledModuleClasses = [];

        foreach ($disabledModules as $module) {
            if ($module->hasClassExtensions()) {
                foreach ($module->getClassExtensions() as $extensionClass) {
                    $disabledModuleClasses[] = $extensionClass->getModuleExtensionClassName();
                }
            }
        }

        return $disabledModuleClasses;
    }

    /**
     * Removes extension metadata from shop.
     */
    public function cleanup()
    {
        $deletedModules = $this->getDeletedExtensions();

        $deletedModuleIds = array_keys($deletedModules);

        $moduleActivationBridge = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleActivationBridgeInterface::class);

        foreach ($deletedModuleIds as $moduleId) {
            if ($moduleActivationBridge->isActive($moduleId, Registry::getConfig()->getShopId())) {
                $moduleActivationBridge->deactivate($moduleId, Registry::getConfig()->getShopId());
            }
        }
    }

    /**
     * Checks module list - if there is extensions that are registered, but extension directory is missing
     *
     * @return array
     */
    public function getDeletedExtensions()
    {
        $aModulesIds = $this->getContainer()
            ->get(ShopConfigurationDaoBridgeInterface::class)
            ->get()
            ->getModuleIdsOfModuleConfigurations();

        $oModule = $this->getModule();
        $aDeletedExt = [];

        foreach ($aModulesIds as $sModuleId) {
            $oModule->setModuleData(['id' => $sModuleId]);
            $aInvalidExtensions = $this->getInvalidExtensions($sModuleId);
            if ($aInvalidExtensions) {
                $aDeletedExt[$sModuleId]['extensions'] = $aInvalidExtensions;
            }
        }

        return $aDeletedExt;
    }

    /**
     * Returns oxModule object.
     *
     * @return \OxidEsales\Eshop\Core\Module\Module
     */
    public function getModule()
    {
        return oxNew(\OxidEsales\Eshop\Core\Module\Module::class);
    }

    /**
     * Parse array of module chains to nested array
     *
     * @param array $modules Module array (config format)
     *
     * @return array
     */
    public function parseModuleChains($modules)
    {
        $moduleArray = [];

        if (is_array($modules)) {
            foreach ($modules as $class => $moduleChain) {
                if (strstr($moduleChain, '&')) {
                    $moduleChain = explode('&', $moduleChain);
                } else {
                    $moduleChain = [$moduleChain];
                }
                $moduleArray[$class] = $moduleChain;
            }
        }

        return $moduleArray;
    }

    /**
     * Returns module extensions.
     *
     * @param string $sModuleId
     *
     * @return array
     */
    public function getModuleExtensions($sModuleId)
    {
        if (!isset($this->_aModuleExtensions)) {
            $aModuleExtension = $this->getActivateModulesWithExtendedClass();
            $oModule = $this->getModule();
            $aExtension = [];
            foreach ($aModuleExtension as $sOxClass => $aFiles) {
                foreach ($aFiles as $sFilePath) {
                    $sId = $oModule->getIdByPath($sFilePath);
                    $aExtension[$sId][$sOxClass][] = $sFilePath;
                }
            }

            $this->_aModuleExtensions = $aExtension;
        }

        return $this->_aModuleExtensions[$sModuleId] ?? [];
    }

    /**
     * Returns shop classes and associated invalid module classes for a given module id
     *
     * @param string $moduleId Module id
     *
     * @return array
     */
    private function getInvalidExtensions($moduleId)
    {
        $extendedShopClasses = $this->getModuleExtensions($moduleId);
        $invalidModuleClasses = [];

        foreach ($extendedShopClasses as $extendedShopClass => $moduleClasses) {
            foreach ($moduleClasses as $moduleClass) {
                /** @var \Composer\Autoload\ClassLoader $composerClassLoader */
                $composerClassLoader = include VENDOR_PATH . 'autoload.php';
                if (!$composerClassLoader->findFile($moduleClass)) {
                    $invalidModuleClasses[$extendedShopClass][] = $moduleClass;
                }
            }
        }

        return $invalidModuleClasses;
    }

    /**
     * @return ModuleConfiguration[]
     */
    private function getDisabledModuleConfigurations(): array
    {
        $container = ContainerFactory::getInstance()->getContainer();

        $moduleConfigurations = $container
            ->get(ShopConfigurationDaoBridgeInterface::class)
            ->get()
            ->getModuleConfigurations();

        $disabledModuleConfigurations = [];

        $moduleStateService = $container->get(ModuleStateServiceInterface::class);

        foreach ($moduleConfigurations as $moduleConfiguration) {
            if (!$moduleStateService->isActive($moduleConfiguration->getId(), Registry::getConfig()->getShopId())) {
                $disabledModuleConfigurations[] = $moduleConfiguration;
            }
        }

        return $disabledModuleConfigurations;
    }

    /**
     * Returns Active module ids which have extensions or files.
     *
     * @return ModuleConfiguration[]
     */
    private function getActiveModuleConfigurations(): array
    {
        $container = ContainerFactory::getInstance()->getContainer();

        $moduleConfigurations = $container
            ->get(ShopConfigurationDaoBridgeInterface::class)
            ->get()
            ->getModuleConfigurations();

        $activeModules = [];

        $moduleStateService = $container->get(ModuleStateServiceInterface::class);

        foreach ($moduleConfigurations as $moduleConfiguration) {
            if ($moduleStateService->isActive($moduleConfiguration->getId(), Registry::getConfig()->getShopId())) {
                $activeModules[] = $moduleConfiguration;
            }
        }

        return $activeModules;
    }

    /**
     * Get activate modules with Extended classes
     *
     * @return array
     */
    private function getActivateModulesWithExtendedClass()
    {
        $extendedClasses = [];

        foreach ($this->getActiveModuleConfigurations() as $moduleConfiguration) {
            if ($moduleConfiguration->hasClassExtensions()) {
                foreach ($moduleConfiguration->getClassExtensions() as $extensions) {
                    if (!isset($extendedClasses[$extensions->getShopClassName()])) {
                        $extendedClasses[$extensions->getShopClassName()] = $extensions->getModuleExtensionClassName();
                    } else {
                        $extendedClasses[$extensions->getShopClassName()] .= '&' . $extensions->getModuleExtensionClassName();
                    }
                }
            }
        }

        return $this->parseModuleChains($extendedClasses);
    }
}
