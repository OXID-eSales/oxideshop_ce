<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;

/**
 * Modules list class.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleList extends \OxidEsales\Eshop\Core\Base
{
    const MODULE_KEY_PATHS = 'Paths';
    const MODULE_KEY_EVENTS = 'Events';
    const MODULE_KEY_VERSIONS = 'Versions';
    const MODULE_KEY_FILES = 'Files';
    const MODULE_KEY_TEMPLATES = 'Templates';
    const MODULE_KEY_EXTENSIONS = 'Extensions';
    const MODULE_KEY_CONTROLLERS = 'Controllers';

    /**
     * Modules info array
     *
     *
     * @var array(id => array)
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
     * Get parsed modules
     *
     * @return array
     */
    public function getModulesWithExtendedClass()
    {
        return $this->parseModuleChains(\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aModules'));
    }

    /**
     * Get active modules path info
     *
     * @return array
     */
    public function getActiveModuleInfo()
    {
        $aModulePaths = $this->getModuleConfigParametersByKey(static::MODULE_KEY_PATHS);

        // Extract module paths from extended classes
        if (!is_array($aModulePaths) || count($aModulePaths) < 1) {
            $aModulePaths = $this->extractModulePaths();
        }

        $aDisabledModules = $this->getDisabledModules();
        if (is_array($aDisabledModules) && count($aDisabledModules) > 0 && count($aModulePaths) > 0) {
            $aModulePaths = array_diff_key($aModulePaths, array_flip($aDisabledModules));
        }

        return $aModulePaths;
    }

    /**
     * Get disabled module paths
     *
     * @return array
     */
    public function getDisabledModuleInfo()
    {
        $aDisabledModules = $this->getDisabledModules();
        $aModulePaths = [];

        if (is_array($aDisabledModules) && count($aDisabledModules) > 0) {
            $aModulePaths = $this->getModuleConfigParametersByKey(static::MODULE_KEY_PATHS);

            // Extract module paths from extended classes
            if (!is_array($aModulePaths) || count($aModulePaths) < 1) {
                $aModulePaths = $this->extractModulePaths();
            }

            if (is_array($aModulePaths) || count($aModulePaths) > 0) {
                $aModulePaths = array_intersect_key($aModulePaths, array_flip($aDisabledModules));
            }
        }

        return $aModulePaths;
    }

    /**
     * Get module id's with versions
     *
     * @return array
     * @deprecated since v6.0.0 (2016-09-15); Use getModuleConfigParametersByKey(ModuleList::MODULE_KEY_VERSIONS) instead.
     */
    public function getModuleVersions()
    {
        return $this->getConfig()->getConfigParam('aModuleVersions');
    }

    /**
     * Get the list of modules
     *
     * @return array
     */
    public function getModules()
    {
        return $this->getConfig()->getConfigParam('aModules');
    }

    /**
     * Get disabled module id's
     *
     * @return array
     */
    public function getDisabledModules()
    {
        return (array) $this->getConfig()->getConfigParam('aDisabledModules');
    }

    /**
     * Get module id's with path
     *
     * @return array
     * @deprecated since v6.0.0 (2016-09-15); Use getModuleConfigParametersByKey(ModuleList::MODULE_KEY_PATHS) instead.
     */
    public function getModulePaths()
    {
        return $this->getConfig()->getConfigParam('aModulePaths');
    }

    /**
     * Get module events
     *
     * @return array
     * @deprecated since v6.0.0 (2016-09-15); Use getModuleConfigParametersByKey(ModuleList::MODULE_KEY_EVENTS) instead.
     */
    public function getModuleEvents()
    {
        return (array) $this->getConfig()->getConfigParam('aModuleEvents');
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
     * Get all modules files paths
     *
     * @return array
     * @deprecated since v6.0.0 (2016-09-15); Use getModuleConfigParametersByKey(ModuleList::MODULE_KEY_FILES) instead.
     */
    public function getModuleFiles()
    {
        return (array) $this->getConfig()->getConfigParam('aModuleFiles');
    }

    /**
     * Get all modules templates paths
     *
     * @return array
     * @deprecated since v6.0.0 (2016-09-15); Use getModuleConfigParametersByKey(ModuleList::MODULE_KEY_TEMPLATES) instead.
     */
    public function getModuleTemplates()
    {
        return $this->getConfig()->getConfigParam('aModuleTemplates');
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
        $disabledModules = $this->getDisabledModules();
        $disabledModuleClasses = [];
        if (isset($disabledModules) && is_array($disabledModules)) {
            //get all disabled module paths
            $extensions = $this->getModuleConfigParametersByKey(static::MODULE_KEY_EXTENSIONS);
            $modules = $this->getModulesWithExtendedClass();
            $modulePaths = $this->getModuleConfigParametersByKey(static::MODULE_KEY_PATHS);

            foreach ($disabledModules as $moduleId) {
                if (!array_key_exists($moduleId, $extensions)) {
                    $path = $modulePaths[$moduleId];
                    if (!isset($path)) {
                        $path = $moduleId;
                    }
                    foreach ($modules as $moduleClasses) {
                        foreach ($moduleClasses as $moduleClass) {
                            if (strpos($moduleClass, $path . "/") === 0) {
                                $disabledModuleClasses[] = $moduleClass;
                            }
                        }
                    }
                } else {
                    $disabledModuleClasses = array_merge($disabledModuleClasses, $extensions[$moduleId]);
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
        $aDeletedModules = $this->getDeletedExtensions();

        //collecting deleted extension IDs
        $aDeletedModuleIds = array_keys($aDeletedModules);

        // removing from aModules config array
        $this->_removeExtensions($aDeletedModuleIds);

        // removing from aDisabledModules array
        $this->_removeFromDisabledModulesArray($aDeletedModuleIds);

        // removing from aModulePaths array
        $this->removeFromModulesArray(static::MODULE_KEY_PATHS, $aDeletedModuleIds);

        // removing from aModuleEvents array
        $this->removeFromModulesArray(static::MODULE_KEY_EVENTS, $aDeletedModuleIds);

        // removing from aModuleVersions array
        $this->removeFromModulesArray(static::MODULE_KEY_VERSIONS, $aDeletedModuleIds);

        // removing from aModuleExtensions array
        $this->removeFromModulesArray(static::MODULE_KEY_EXTENSIONS, $aDeletedModuleIds);

        // removing from aModuleFiles array
        $this->removeFromModulesArray(static::MODULE_KEY_FILES, $aDeletedModuleIds);

        // removing from aModuleTemplates array
        $this->removeFromModulesArray(static::MODULE_KEY_TEMPLATES, $aDeletedModuleIds);

        // removing from aModuleControllers array
        $this->removeFromModulesArray(static::MODULE_KEY_CONTROLLERS, $aDeletedModuleIds);

        //removing from config tables and templates blocks table
        $this->_removeFromDatabase($aDeletedModuleIds);

        //Remove from caches.
        \OxidEsales\Eshop\Core\Module\ModuleVariablesLocator::resetModuleVariables();
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
     * @return oxModule
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
     * Removes extension by given modules ids.
     *
     * @param array $aModuleIds Modules ids which must be deleted from config.
     */
    protected function _removeExtensions($aModuleIds)
    {
        $aModuleExtensions = $this->getModulesWithExtendedClass();
        $aExtensionsToDelete = [];
        foreach ($aModuleIds as $sModuleId) {
            $aExtensionsToDelete = array_merge_recursive($aExtensionsToDelete, $this->getModuleExtensions($sModuleId));
        }

        $aUpdatedExtensions = $this->diffModuleArrays($aModuleExtensions, $aExtensionsToDelete);
        $aUpdatedExtensionsChains = $this->buildModuleChains($aUpdatedExtensions);

        $this->getConfig()->saveShopConfVar('aarr', 'aModules', $aUpdatedExtensionsChains);
    }

    /**
     * Removes extension from disabled modules array
     *
     * @param array $aDeletedExtIds Deleted extension id's of array
     */
    protected function _removeFromDisabledModulesArray($aDeletedExtIds)
    {
        $oConfig = $this->getConfig();
        $aDisabledExtensionIds = $this->getDisabledModules();
        $aDisabledExtensionIds = array_diff($aDisabledExtensionIds, $aDeletedExtIds);
        $oConfig->saveShopConfVar('arr', 'aDisabledModules', $aDisabledExtensionIds);
    }

    /**
     * Removes extension from given modules array.
     *
     * @param string $key            Module array key.
     * @param array  $aDeletedModule Deleted extensions ID's.
     */
    protected function removeFromModulesArray($key, $aDeletedModule)
    {
        $array = $this->getModuleConfigParametersByKey($key);

        foreach ($aDeletedModule as $sDeletedModuleId) {
            if (isset($array[$sDeletedModuleId])) {
                unset($array[$sDeletedModuleId]);
            }
        }

        $this->getConfig()->saveShopConfVar('aarr', 'aModule' . $key, $array);
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
        return (array) $this->getConfig()->getConfigParam('aModule' . $key);
    }

    /**
     * Removes extension from database - oxConfig, oxConfigDisplay and oxTplBlocks tables
     *
     * @todo extract oxtplblocks query to ModuleTemplateBlockRepository
     *
     * @param array $aDeletedExtIds deleted extensions ID's
     *
     * @return null
     */
    protected function _removeFromDatabase($aDeletedExtIds)
    {
        if (!is_array($aDeletedExtIds) || !count($aDeletedExtIds)) {
            return;
        }

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $aConfigIds = $sDelExtIds = [];
        foreach ($aDeletedExtIds as $sDeletedExtId) {
            $aConfigIds[] = $oDb->quote('module:' . $sDeletedExtId);
            $sDelExtIds[] = $oDb->quote($sDeletedExtId);
        }

        $sConfigIds = implode(', ', $aConfigIds);
        $sDelExtIds = implode(', ', $sDelExtIds);

        $aSql[] = "DELETE FROM oxconfig where oxmodule IN ($sConfigIds)";
        $aSql[] = "DELETE FROM oxconfigdisplay where oxcfgmodule IN ($sConfigIds)";
        $aSql[] = "DELETE FROM oxtplblocks where oxmodule IN ($sDelExtIds)";

        foreach ($aSql as $sQuery) {
            $oDb->execute($sQuery);
        }
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
        $container = ContainerFactory::getInstance()->getContainer();
        $shopConfiguration = $container->get(ShopConfigurationDaoBridgeInterface::class)->get();

        $modules = [];

        foreach ($shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
            $module = $this->getModule();
            $module->load($moduleConfiguration->getId());
            $modules[$moduleConfiguration->getId()] = $module;
        }

        $this->_aModules = $modules;
        uasort($this->_aModules, [$this, '_sortModules']);

        return $modules;
    }

    /**
     * Gets module validator factory.
     *
     * @return oxModuleValidatorFactory
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
        $aModuleIdsFromExtensions = $this->_getModuleIdsFromExtensions($this->getModulesWithExtendedClass());
        $aModuleIdsFromFiles = array_keys($this->getModuleConfigParametersByKey(static::MODULE_KEY_FILES));

        return array_unique(array_merge($aModuleIdsFromExtensions, $aModuleIdsFromFiles));
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
            $aModuleExtension = $this->getConfig()->getModulesWithExtendedClass();
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

        return $this->_aModuleExtensions[$sModuleId] ? $this->_aModuleExtensions[$sModuleId] : [];
    }

    /**
     * Callback function for sorting module objects by name.
     *
     * @param object $oModule1 module object
     * @param object $oModule2 module object
     *
     * @return bool
     */
    protected function _sortModules($oModule1, $oModule2)
    {
        return strcasecmp($oModule1->getTitle(), $oModule2->getTitle());
    }

    /**
     * Checks if directory is vendor directory.
     *
     * @param string $sModuleDir dir path
     *
     * @return bool
     */
    protected function _isVendorDir($sModuleDir)
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
     * Checks if module extends any shop class.
     *
     * @param string $sModuleDir dir path
     *
     * @return bool
     */
    protected function _extendsClasses($sModuleDir)
    {
        $aModules = $this->getConfig()->getConfigParam('aModules');
        if (is_array($aModules)) {
            $sModules = implode('&', $aModules);

            if (preg_match("@(^|&+)" . $sModuleDir . "\b@", $sModules)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Saving module path info. Module path is saved to config variable "aModulePaths".
     *
     * @param string $sModuleId   Module ID
     * @param string $sModulePath Module path
     */
    protected function _saveModulePath($sModuleId, $sModulePath)
    {
        $aModulePaths = $this->getModuleConfigParametersByKey(static::MODULE_KEY_PATHS);

        $aModulePaths[$sModuleId] = $sModulePath;
        $this->getConfig()->saveShopConfVar('aarr', 'aModulePaths', $aModulePaths);
    }

    /**
     * Returns module ids which have extensions.
     *
     * @param array $aData Data
     *
     * @return array
     */
    private function _getModuleIdsFromExtensions($aData)
    {
        $aModuleIds = [];
        $oModule = $this->getModule();
        foreach ($aData as $aModule) {
            foreach ($aModule as $sFilePath) {
                $sModuleId = $oModule->getIdByPath($sFilePath);
                $aModuleIds[] = $sModuleId;
            }
        }

        return $aModuleIds;
    }

    /**
     * Returns shop classes and associated invalid module classes for a given module id
     *
     * @param string $moduleId Module id
     *
     * @return array
     */
    private function _getInvalidExtensions($moduleId)
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
     *
     * @deprecated since v6.0 (2017-03-14); This method will be removed in the future.
     */
    private function backwardsCompatibleGetInvalidExtensions($moduleClass, &$invalidModuleClasses, $extendedShopClass)
    {
        $moduleClassFile = $this->getConfig()->getModulesDir() . $moduleClass . '.php';
        if (!is_readable($moduleClassFile)) {
            $invalidModuleClasses[$extendedShopClass][] = $moduleClass;
        }
    }
}
