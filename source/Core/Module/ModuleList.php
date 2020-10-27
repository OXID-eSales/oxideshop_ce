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
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleList extends \OxidEsales\Eshop\Core\Base
{
    const MODULE_KEY_PATHS = 'Paths';
    const MODULE_KEY_EVENTS = 'Events';
    const MODULE_KEY_VERSIONS = 'Versions';
    const MODULE_KEY_TEMPLATES = 'Templates';
    const MODULE_KEY_EXTENSIONS = 'Extensions';
    const MODULE_KEY_CONTROLLERS = 'Controllers';

    /**
     * Modules info array
     *
     *
     * @var array<string, array>
     */
    protected $_aModules = [];

    /**
     * All module extensions.
     *
     * @var array
     */
    protected $_aModuleExtensions = null;

    /**
     * List of files that should be skipped while scanning module dir.
     *
     * @var array
     */
    protected $_aSkipFiles = ['functions.php', 'vendormetadata.php'];

    /**
     * Return array of modules
     *
     * @return array
     */
    public function getList()
    {
        return $this->_aModules;
    }

    /**
     * Get all modules with Extended classes
     *
     * @return array
     */
    public function getModulesWithExtendedClass()
    {
        $moduleExtensions = [];

        $container = ContainerFactory::getInstance()->getContainer();
        $moduleConfigurations = $container
            ->get(ShopConfigurationDaoBridgeInterface::class)
            ->get()
            ->getModuleConfigurations();

        foreach ($moduleConfigurations as $moduleConfiguration) {
            if ($moduleConfiguration->hasClassExtensions()) {
                foreach ($moduleConfiguration->getClassExtensions() as $extensions) {
                    if (!isset($moduleExtensions[$extensions->getShopClassName()])) {
                        $moduleExtensions[$extensions->getShopClassName()] = $extensions->getModuleExtensionClassName();
                    } else {
                        $moduleExtensions[$extensions->getShopClassName()] .= '&' . $extensions->getModuleExtensionClassName();
                    }
                }
            }
        }

        return $this->parseModuleChains($moduleExtensions);
    }

    /**
     * Get active modules path info
     *
     * @return array
     */
    public function getActiveModuleInfo()
    {
        return $this->getModuleConfigParametersByKey(static::MODULE_KEY_PATHS);
    }

    /**
     * Get disabled module paths
     *
     * @return array
     */
    public function getDisabledModuleInfo()
    {
        $modulePaths = [];

        foreach ($this->getDisabledModuleConfigurations() as $moduleConfiguration) {
            $modulePaths[$moduleConfiguration->getId()] = $moduleConfiguration->getPath();
        }

        return $modulePaths;
    }

    /**
     * Get module id's with versions
     *
     * @return array
     */
    public function getModuleVersions()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aModuleVersions');
    }

    /**
     * Get the list of modules
     *
     * @return array
     */
    public function getModules()
    {
        $extendedClasses = [];

        $container = ContainerFactory::getInstance()->getContainer();
        $moduleConfigurations = $container
            ->get(ShopConfigurationDaoBridgeInterface::class)
            ->get()
            ->getModuleConfigurations();

        foreach ($moduleConfigurations as $moduleConfiguration) {
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


        return $extendedClasses;
    }

    /**
     * Get disabled module id's
     *
     * @return array
     */
    public function getDisabledModules()
    {
        $moduleIds = [];

        foreach ($this->getDisabledModuleConfigurations() as $moduleConfiguration) {
            $moduleIds[] = $moduleConfiguration->getId();
        }

        return $moduleIds;
    }

    /**
     * Get module id's with path
     *
     * @return array
     */
    public function getModulePaths()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aModulePaths');
    }

    /**
     * Get module events
     *
     * @return array
     */
    public function getModuleEvents()
    {
        return (array) \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aModuleEvents');
    }

    /**
     * Extract module id's with paths from extended classes.
     *
     * @return array
     */
    public function extractModulePaths()
    {
        $aModules = $this->getModulesWithExtendedClass();
        $aModulePaths = [];

        if (is_array($aModules) && count($aModules) > 0) {
            foreach ($aModules as $aModuleClasses) {
                foreach ($aModuleClasses as $sModule) {
                    $sModuleId = substr($sModule, 0, strpos($sModule, "/"));
                    $aModulePaths[$sModuleId] = $sModuleId;
                }
            }
        }

        return $aModulePaths;
    }

    /**
     * Returns disabled module classes with path using config aModules
     * and aModulePaths.
     * aModules has all extended classes
     * aModulePaths has module id to main path array
     *
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
        $oModuleValidatorFactory = $this->getModuleValidatorFactory();
        $oModuleMetadataValidator = $oModuleValidatorFactory->getModuleMetadataValidator();
        $aModulesIds = $this->getModuleIds();
        $oModule = $this->getModule();
        $aDeletedExt = [];

        foreach ($aModulesIds as $sModuleId) {
            $oModule->setModuleData(['id' => $sModuleId]);
            if (!$oModuleMetadataValidator->validate($oModule)) {
                $aDeletedExt[$sModuleId]['files'] = [$sModuleId . '/metadata.php'];
            } else {
                $aInvalidExtensions = $this->_getInvalidExtensions($sModuleId);
                if ($aInvalidExtensions) {
                    $aDeletedExt[$sModuleId]['extensions'] = $aInvalidExtensions;
                }
            }
        }

        return $aDeletedExt;
    }

    /**
     * Diff two nested module arrays together so that the values of
     * $aRmModuleArray are removed from $aAllModuleArray
     *
     * @param array $aAllModuleArray All Module array (nested format)
     * @param array $aRemModuleArray Remove Module array (nested format)
     *
     * @return array
     */
    public function diffModuleArrays($aAllModuleArray, $aRemModuleArray)
    {
        if (is_array($aAllModuleArray) && is_array($aRemModuleArray)) {
            foreach ($aAllModuleArray as $sClass => $aModuleChain) {
                if (!is_array($aModuleChain)) {
                    $aModuleChain = [$aModuleChain];
                }
                if (isset($aRemModuleArray[$sClass])) {
                    if (!is_array($aRemModuleArray[$sClass])) {
                        $aRemModuleArray[$sClass] = [$aRemModuleArray[$sClass]];
                    }
                    $aAllModuleArray[$sClass] = [];
                    foreach ($aModuleChain as $sModule) {
                        if (!in_array($sModule, $aRemModuleArray[$sClass])) {
                            $aAllModuleArray[$sClass][] = $sModule;
                        }
                    }
                    if (!count($aAllModuleArray[$sClass])) {
                        unset($aAllModuleArray[$sClass]);
                    }
                } else {
                    $aAllModuleArray[$sClass] = $aModuleChain;
                }
            }
        }

        return $aAllModuleArray;
    }

    /**
     * Build module chains from nested array
     *
     * @param array $aModuleArray Module array (nested format)
     *
     * @return array
     */
    public function buildModuleChains($aModuleArray)
    {
        $aModules = [];
        if (is_array($aModuleArray)) {
            foreach ($aModuleArray as $sClass => $aModuleChain) {
                $aModules[$sClass] = implode('&', $aModuleChain);
            }
        }

        return $aModules;
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
     * Gets Module config parameters by key
     *
     * e.g. to get 'aModulePaths' call $obj->getModuleConfigParametersByKey(ModuleList::MODULE_KEY_PATHS)
     *
     * @param string $key Key
     *
     * @return array module config parameters for given key
     */
    public function getModuleConfigParametersByKey($key)
    {
        return (array) \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aModule' . $key);
    }

    /**
     * Scans modules dir and returns collected modules list.
     * Recursively loads also modules that are in vendor directory.
     *
     * @param string $sModulesDir Main module dir path
     * @param string $sVendorDir  Vendor directory name
     *
     * @return array
     */
    public function getModulesFromDir($sModulesDir, $sVendorDir = null)
    {
        $sModulesDir = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->normalizeDir($sModulesDir);

        foreach (glob($sModulesDir . '*') as $sModuleDirPath) {
            $sModuleDirPath .= (is_dir($sModuleDirPath)) ? '/' : '';
            $sModuleDirName = basename($sModuleDirPath);

            // skipping some file
            if (in_array($sModuleDirName, $this->_aSkipFiles) || (!is_dir($sModuleDirPath) && substr($sModuleDirName, -4) != ".php")) {
                continue;
            }

            if ($this->_isVendorDir($sModuleDirPath)) {
                // scanning modules vendor directory
                $this->getModulesFromDir($sModuleDirPath, basename($sModuleDirPath));
            } else {
                // loading module info
                $oModule = $this->getModule();
                $sModuleDirName = (!empty($sVendorDir)) ? $sVendorDir . '/' . $sModuleDirName : $sModuleDirName;
                if ($oModule->loadByDir($sModuleDirName)) {
                    $sModuleId = $oModule->getId();
                    $this->_aModules[$sModuleId] = $oModule;
                }
            }
        }
        // sorting by name
        if ($this->_aModules !== null) {
            uasort($this->_aModules, [$this, '_sortModules']);
        }

        return $this->_aModules;
    }

    /**
     * Gets module validator factory.
     *
     * @return \OxidEsales\Eshop\Core\Module\ModuleValidatorFactory
     */
    public function getModuleValidatorFactory()
    {
        return oxNew(\OxidEsales\Eshop\Core\Module\ModuleValidatorFactory::class);
    }

    /**
     * Returns module ids which have extensions or files.
     *
     * @return array
     */
    public function getModuleIds()
    {
        $container = ContainerFactory::getInstance()->getContainer();

        return $container
            ->get(ShopConfigurationDaoBridgeInterface::class)
            ->get()
            ->getModuleIdsOfModuleConfigurations();
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
     * Callback function for sorting module objects by name.
     *
     * @param object $oModule1 module object
     * @param object $oModule2 module object
     *
     * @return bool
     * @deprecated underscore prefix violates PSR12, will be renamed to "sortModules" in next major
     */
    protected function _sortModules($oModule1, $oModule2) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return strcasecmp($oModule1->getTitle(), $oModule2->getTitle());
    }

    /**
     * Checks if directory is vendor directory.
     *
     * @param string $sModuleDir dir path
     *
     * @return bool
     * @deprecated underscore prefix violates PSR12, will be renamed to "isVendorDir" in next major
     */
    protected function _isVendorDir($sModuleDir) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        if (!is_dir($sModuleDir)) {
            return false;
        }

        $currentDirectoryContents = scandir($sModuleDir);
        $currentDirectoryContents = array_diff($currentDirectoryContents, ['.', '..']);
        foreach ($currentDirectoryContents as $entry) {
            if (is_dir("$sModuleDir/$entry") && file_exists("$sModuleDir/$entry/metadata.php")) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns shop classes and associated invalid module classes for a given module id
     *
     * @param string $moduleId Module id
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "getInvalidExtensions" in next major
     */
    private function _getInvalidExtensions($moduleId) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $extendedShopClasses = $this->getModuleExtensions($moduleId);
        $invalidModuleClasses = [];

        foreach ($extendedShopClasses as $extendedShopClass => $moduleClasses) {
            foreach ($moduleClasses as $moduleClass) {
                if (\OxidEsales\Eshop\Core\NamespaceInformationProvider::isNamespacedClass($moduleClass)) {
                    /** @var \Composer\Autoload\ClassLoader $composerClassLoader */
                    $composerClassLoader = include VENDOR_PATH . 'autoload.php';
                    if (!$composerClassLoader->findFile($moduleClass)) {
                        $invalidModuleClasses[$extendedShopClass][] = $moduleClass;
                    }
                } else {
                    /** Note: $aDeletedExt is passed by reference */
                    $this->backwardsCompatibleGetInvalidExtensions($moduleClass, $invalidModuleClasses, $extendedShopClass);
                }
            }
        }

        return $invalidModuleClasses;
    }

    /**
     * Backwards compatible version of self::_getInvalidExtensions()
     *
     * @param string $moduleClass          The module class, which extends a given shop class
     * @param array  $invalidModuleClasses The Collection of module classes , which are marked as deleted
     *                                     Note: This parameter is passed by reference
     * @param string $extendedShopClass    The shop class, which is extended by the module class
     */
    private function backwardsCompatibleGetInvalidExtensions($moduleClass, &$invalidModuleClasses, $extendedShopClass)
    {
        $moduleClassFile = \OxidEsales\Eshop\Core\Registry::getConfig()->getModulesDir() . $moduleClass . '.php';
        if (!is_readable($moduleClassFile)) {
            $invalidModuleClasses[$extendedShopClass][] = $moduleClass;
        }
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
