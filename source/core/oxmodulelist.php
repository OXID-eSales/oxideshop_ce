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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Modules list class.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class oxModuleList extends oxSuperCfg
{

    /**
     * Modules info array
     *
     *
     * @var array(id => array)
     */
    protected $_aModules = array();

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
    protected $_aSkipFiles = array('functions.php', 'vendormetadata.php');

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
        return $this->getConfig()->getModulesWithExtendedClass();
    }

    /**
     * Get active modules path info
     *
     * @return array
     */
    public function getActiveModuleInfo()
    {
        $aModulePaths = $this->getModulePaths();

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
        $aModulePaths = array();

        if (is_array($aDisabledModules) && count($aDisabledModules) > 0) {
            $aModulePaths = $this->getModulePaths();

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
     */
    public function getModulePaths()
    {
        return $this->getConfig()->getConfigParam('aModulePaths');
    }

    /**
     * Get module events
     *
     * @return array
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
        $aModulePaths = array();

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
     */
    public function getModuleFiles()
    {
        return (array) $this->getConfig()->getConfigParam('aModuleFiles');
    }

    /**
     * Get all modules templates paths
     *
     * @return array
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
        $aDisabledModules = $this->getDisabledModules();
        $aModules = $this->getModulesWithExtendedClass();
        $aModulePaths = $this->getModulePaths();

        $aDisabledModuleClasses = array();
        if (isset($aDisabledModules) && is_array($aDisabledModules)) {
            //get all disabled module paths
            foreach ($aDisabledModules as $sId) {
                $sPath = $aModulePaths[$sId];
                if (!isset($sPath)) {
                    $sPath = $sId;
                }
                foreach ($aModules as $aModuleClasses) {
                    foreach ($aModuleClasses as $sModuleClass) {
                        if (strpos($sModuleClass, $sPath . "/") === 0) {
                            $aDisabledModuleClasses[] = $sModuleClass;
                        }
                    }
                }
            }
        }

        return $aDisabledModuleClasses;
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
        $this->_removeFromModulesPathsArray($aDeletedModuleIds);

        // removing from aModuleEvents array
        $this->_removeFromModulesEventsArray($aDeletedModuleIds);

        // removing from aModuleVersions array
        $this->_removeFromModulesVersionsArray($aDeletedModuleIds);

        // removing from aModuleFiles array
        $this->_removeFromModulesFilesArray($aDeletedModuleIds);

        // removing from aModuleTemplates array
        $this->_removeFromModulesTemplatesArray($aDeletedModuleIds);

        //removing from config tables and templates blocks table
        $this->_removeFromDatabase($aDeletedModuleIds);
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
        $aDeletedExt = array();

        foreach ($aModulesIds as $sModuleId) {
            $oModule->setModuleData(array('id' => $sModuleId));
            if (!$oModuleMetadataValidator->validate($oModule)) {
                $aDeletedExt[$sModuleId]['files'] = array($sModuleId . '/metadata.php');
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
                    $aModuleChain = array($aModuleChain);
                }
                if (isset($aRemModuleArray[$sClass])) {
                    if (!is_array($aRemModuleArray[$sClass])) {
                        $aRemModuleArray[$sClass] = array($aRemModuleArray[$sClass]);
                    }
                    $aAllModuleArray[$sClass] = array();
                    foreach ($aModuleChain as $sModule) {
                        if (!in_array($sModule, $aRemModuleArray[$sClass])) {
                            $aAllModuleArray[$sClass][] = $sModule;
                        }
                    }
                    if (!count($aAllModuleArray[$sClass])) {
                        unset ($aAllModuleArray[$sClass]);
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
        $aModules = array();
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
        return oxNew('oxModule');
    }

    /**
     * Removes extension by given modules ids.
     *
     * @param array $aModuleIds Modules ids which must be deleted from config.
     */
    protected function _removeExtensions($aModuleIds)
    {
        $aModuleExtensions = $this->getModulesWithExtendedClass();
        $aExtensionsToDelete = array();
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
     * Removes extension from modules paths array
     *
     * @param array $aDeletedModule deleted extensions ID's
     */
    protected function _removeFromModulesPathsArray($aDeletedModule)
    {
        $aModulePaths = $this->getModulePaths();

        foreach ($aDeletedModule as $sDeletedModuleId) {
            if (isset($aModulePaths[$sDeletedModuleId])) {
                unset($aModulePaths[$sDeletedModuleId]);
            }
        }

        $this->getConfig()->saveShopConfVar('aarr', 'aModulePaths', $aModulePaths);
    }

    /**
     * Removes extension from modules versions array
     *
     * @param array $aDeletedModule deleted extensions ID's
     */
    protected function _removeFromModulesVersionsArray($aDeletedModule)
    {
        $aModuleVersions = $this->getModuleVersions();

        foreach ($aDeletedModule as $sDeletedModuleId) {
            if (isset($aModuleVersions[$sDeletedModuleId])) {
                unset($aModuleVersions[$sDeletedModuleId]);
            }
        }

        $this->getConfig()->saveShopConfVar('aarr', 'aModuleVersions', $aModuleVersions);
    }

    /**
     * Removes extension from modules events array
     *
     * @param array $aDeletedModule deleted extensions ID's
     */
    protected function _removeFromModulesEventsArray($aDeletedModule)
    {
        $aModuleEvents = $this->getModuleEvents();

        foreach ($aDeletedModule as $sDeletedModuleId) {
            if (isset($aModuleEvents[$sDeletedModuleId])) {
                unset($aModuleEvents[$sDeletedModuleId]);
            }
        }

        $this->getConfig()->saveShopConfVar('aarr', 'aModuleEvents', $aModuleEvents);
    }

    /**
     * Removes extension from modules files array
     *
     * @param array $aDeletedModule deleted extensions ID's
     */
    protected function _removeFromModulesFilesArray($aDeletedModule)
    {
        $aModuleFiles = $this->getModuleFiles();

        foreach ($aDeletedModule as $sDeletedModuleId) {
            if (isset($aModuleFiles[$sDeletedModuleId])) {
                unset($aModuleFiles[$sDeletedModuleId]);
            }
        }

        $this->getConfig()->saveShopConfVar('aarr', 'aModuleFiles', $aModuleFiles);
    }

    /**
     * Removes extension from modules templates array
     *
     * @param array $aDeletedModule deleted extensions ID's
     */
    protected function _removeFromModulesTemplatesArray($aDeletedModule)
    {
        $aModuleTemplates = $this->getModuleTemplates();

        foreach ($aDeletedModule as $sDeletedModuleId) {
            if (isset($aModuleTemplates[$sDeletedModuleId])) {
                unset($aModuleTemplates[$sDeletedModuleId]);
            }
        }

        $this->getConfig()->saveShopConfVar('aarr', 'aModuleTemplates', $aModuleTemplates);
    }

    /**
     * Removes extension from database - oxConfig, oxConfigDisplay and oxTplBlocks tables
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

        $oDb = oxDb::getDb();

        $aConfigIds = $sDelExtIds = array();
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
        $sModulesDir = oxRegistry::get('oxUtilsFile')->normalizeDir($sModulesDir);

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

                    $aModulePaths = $this->getModulePaths();

                    if (!is_array($aModulePaths) || !array_key_exists($sModuleId, $aModulePaths)) {
                        // saving module path info
                        $this->_saveModulePath($sModuleId, $sModuleDirName);

                        //checking if this is new module and if it extends any eshop class
                        if (!$this->_extendsClasses($sModuleDirName)) {
                            // if not - marking it as disabled by default

                            /** @var oxModuleCache $oModuleCache */
                            $oModuleCache = oxNew('oxModuleCache', $oModule);
                            /** @var oxModuleInstaller $oModuleInstaller */
                            $oModuleInstaller = oxNew('oxModuleInstaller', $oModuleCache);

                            $oModuleInstaller->deactivate($oModule);
                        }
                    }
                }
            }
        }
        // sorting by name
        if ($this->_aModules !== null) {
            uasort($this->_aModules, array($this, '_sortModules'));
        }

        return $this->_aModules;
    }

    /**
     * Gets module validator factory.
     *
     * @return oxModuleValidatorFactory
     */
    public function getModuleValidatorFactory()
    {
        return oxNew('oxModuleValidatorFactory');
    }

    /**
     * Returns module ids which have extensions or files.
     *
     * @return array
     */
    public function getModuleIds()
    {
        $aModuleIdsFromExtensions = $this->_getModuleIdsFromExtensions($this->getModulesWithExtendedClass());
        $aModuleIdsFromFiles = array_keys($this->getModuleFiles());

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
            $aExtension = array();
            foreach ($aModuleExtension as $sOxClass => $aFiles) {
                foreach ($aFiles as $sFilePath) {
                    $sId = $oModule->getIdByPath($sFilePath);
                    $aExtension[$sId][$sOxClass][] = $sFilePath;
                }
            }

            $this->_aModuleExtensions = $aExtension;
        }

        return $this->_aModuleExtensions[$sModuleId] ? $this->_aModuleExtensions[$sModuleId] : array();
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
        if (is_dir($sModuleDir) && file_exists($sModuleDir . 'vendormetadata.php')) {
            return true;
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
        $aModulePaths = $this->getModulePaths();

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
        $aModuleIds = array();
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
     * Returns invalid extensions array by module id.
     *
     * @param string $sModuleId Module id
     *
     * @return array
     */
    private function _getInvalidExtensions($sModuleId)
    {
        $aModules = $this->getModuleExtensions($sModuleId);
        $aDeletedExt = array();

        foreach ($aModules as $sOxClass => $aModulesList) {
            foreach ($aModulesList as $sModulePath) {
                $sExtPath = $this->getConfig()->getModulesDir() . $sModulePath . '.php';
                if (!file_exists($sExtPath)) {
                    $aDeletedExt[$sOxClass][] = $sModulePath;
                }
            }
        }

        return $aDeletedExt;
    }
}
