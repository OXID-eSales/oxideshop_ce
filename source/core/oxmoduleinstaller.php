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
 * Modules installer class.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class oxModuleInstaller extends oxSuperCfg
{

    /**
     * @var oxModuleCache
     */
    protected $_oModuleCache;

    /**
     * Sets dependencies.
     *
     * @param oxModuleCache $oxModuleCache
     */
    public function __construct(oxModuleCache $oxModuleCache = null)
    {
        $this->setModuleCache($oxModuleCache);
    }

    /**
     * Sets module cache.
     *
     * @param oxModuleCache $oModuleCache
     */
    public function setModuleCache($oModuleCache)
    {
        $this->_oModuleCache = $oModuleCache;
    }

    /**
     * Gets module cache.
     *
     * @return oxModuleCache
     */
    public function getModuleCache()
    {
        return $this->_oModuleCache;
    }

    /**
     * Activate extension by merging module class inheritance information with shop module array
     *
     * @param oxModule $oModule
     *
     * @return bool
     */
    public function activate(oxModule $oModule)
    {
        $blResult = false;

        $sModuleId = $oModule->getId();

        if ($sModuleId) {
            $this->_addExtensions($oModule);
            $this->_removeFromDisabledList($sModuleId);

            $this->_addTemplateBlocks($oModule->getInfo("blocks"), $sModuleId);
            $this->_addModuleFiles($oModule->getInfo("files"), $sModuleId);
            $this->_addTemplateFiles($oModule->getInfo("templates"), $sModuleId);
            $this->_addModuleSettings($oModule->getInfo("settings"), $sModuleId);
            $this->_addModuleVersion($oModule->getInfo("version"), $sModuleId);
            $this->_addModuleEvents($oModule->getInfo("events"), $sModuleId);

            //resets cache
            if ($this->getModuleCache()) {
                $this->getModuleCache()->resetCache();

            }

            $this->_callEvent('onActivate', $oModule->getId());

            $blResult = true;
        }

        return $blResult;
    }

    /**
     * Deactivate extension by adding disable module class information to disabled module array
     *
     * @param oxModule $oModule
     *
     * @return bool
     */
    public function deactivate(oxModule $oModule)
    {
        $blResult = false;

        $sModuleId = $oModule->getId();

        if ($sModuleId) {
            $sModuleId = $oModule->getId();
            $this->_callEvent('onDeactivate', $sModuleId);

            $this->_addToDisabledList($sModuleId);

            //removing recoverable options
            $this->_deleteBlock($sModuleId);
            $this->_deleteTemplateFiles($sModuleId);
            $this->_deleteModuleFiles($sModuleId);
            $this->_deleteModuleEvents($sModuleId);
            $this->_deleteModuleVersions($sModuleId);

            //resets cache
            if ($this->getModuleCache()) {
                $this->getModuleCache()->resetCache();
            }

            $blResult = true;
        }

        return $blResult;
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
     * Add module to disable list
     *
     * @param string $sModuleId Module id
     */
    protected function _addToDisabledList($sModuleId)
    {
        $aDisabledModules = (array) $this->getConfig()->getConfigParam('aDisabledModules');

        $aModules = array_merge($aDisabledModules, array($sModuleId));
        $aModules = array_unique($aModules);

        $this->_saveToConfig('aDisabledModules', $aModules, 'arr');
    }

    /**
     * Removes extension from modules array
     *
     * @param string $sModuleId Module id
     */
    protected function _deleteModule($sModuleId)
    {
        $aExt = $this->getConfig()->getModulesWithExtendedClass();

        $aUpdatedExt = $this->diffModuleArrays($aExt, $sModuleId);
        $aUpdatedExt = $this->buildModuleChains($aUpdatedExt);

        $this->getConfig()->saveShopConfVar('aarr', 'aModules', $aUpdatedExt);
    }

    /**
     * Deactivates or activates oxBlocks of a module
     *
     * @param string $sModuleId Module id
     */
    protected function _deleteBlock($sModuleId)
    {
        $oDb = oxDb::getDb();
        $sShopId = $this->getConfig()->getShopId();
        $oDb->execute("DELETE FROM `oxtplblocks` WHERE `oxmodule` =" . $oDb->quote($sModuleId) . " AND `oxshopid` = " . $oDb->quote($sShopId));
    }

    /**
     * Add module template files to config for smarty.
     *
     * @param string $sModuleId Module id
     */
    protected function _deleteTemplateFiles($sModuleId)
    {
        $aTemplates = (array) $this->getConfig()->getConfigParam('aModuleTemplates');
        unset($aTemplates[$sModuleId]);

        $this->_saveToConfig('aModuleTemplates', $aTemplates);
    }

    /**
     * Add module files
     *
     * @param string $sModuleId Module id
     */
    protected function _deleteModuleFiles($sModuleId)
    {
        $aFiles = (array) $this->getConfig()->getConfigParam('aModuleFiles');
        unset($aFiles[$sModuleId]);

        $this->_saveToConfig('aModuleFiles', $aFiles);
    }

    /**
     * Removes module events
     *
     * @param string $sModuleId Module id
     */
    protected function _deleteModuleEvents($sModuleId)
    {
        $aEvents = (array) $this->getConfig()->getConfigParam('aModuleEvents');
        unset($aEvents[$sModuleId]);

        $this->_saveToConfig('aModuleEvents', $aEvents);
    }

    /**
     * Removes module versions
     *
     * @param string $sModuleId Module id
     */
    protected function _deleteModuleVersions($sModuleId)
    {
        $aVersions = (array) $this->getConfig()->getConfigParam('aModuleVersions');
        unset($aVersions[$sModuleId]);

        $this->_saveToConfig('aModuleVersions', $aVersions);
    }

    /**
     * Add extension to module
     *
     * @param oxModule $oModule
     */
    protected function _addExtensions(oxModule $oModule)
    {
        $aModules = $this->_removeNotUsedExtensions($this->getModulesWithExtendedClass(), $oModule);

        if ($oModule->hasExtendClass()) {
            $aAddModules = $oModule->getExtensions();
            $aModules = $this->_mergeModuleArrays($aModules, $aAddModules);
        }

        $aModules = $this->buildModuleChains($aModules);

        $this->_saveToConfig('aModules', $aModules);
    }

    /**
     * Merge two nested module arrays together so that the values of
     * $aAddModuleArray are appended to the end of the $aAllModuleArray
     *
     * @param array $aAllModuleArray All Module array (nested format)
     * @param array $aAddModuleArray Added Module array (nested format)
     *
     * @return array
     */
    protected function _mergeModuleArrays($aAllModuleArray, $aAddModuleArray)
    {
        if (is_array($aAllModuleArray) && is_array($aAddModuleArray)) {
            foreach ($aAddModuleArray as $sClass => $aModuleChain) {
                if (!is_array($aModuleChain)) {
                    $aModuleChain = array($aModuleChain);
                }
                if (isset($aAllModuleArray[$sClass])) {
                    foreach ($aModuleChain as $sModule) {
                        if (!in_array($sModule, $aAllModuleArray[$sClass])) {
                            $aAllModuleArray[$sClass][] = $sModule;
                        }
                    }
                } else {
                    $aAllModuleArray[$sClass] = $aModuleChain;
                }
            }
        }

        return $aAllModuleArray;
    }

    /**
     * Removes module from disabled module list
     *
     * @param string $sModuleId Module id
     */
    protected function _removeFromDisabledList($sModuleId)
    {
        $aDisabledModules = (array) $this->getConfig()->getConfigParam('aDisabledModules');

        if (isset($aDisabledModules) && is_array($aDisabledModules)) {
            $aDisabledModules = array_diff($aDisabledModules, array($sModuleId));
            $this->_saveToConfig('aDisabledModules', $aDisabledModules, 'arr');
        }
    }

    /**
     * Add module templates to database.
     *
     * @param array  $aModuleBlocks Module blocks array
     * @param string $sModuleId     Module id
     */
    protected function _addTemplateBlocks($aModuleBlocks, $sModuleId)
    {
        $sShopId = $this->getConfig()->getShopId();
        $oDb = oxDb::getDb();

        if (is_array($aModuleBlocks)) {

            foreach ($aModuleBlocks as $aValue) {
                $sOxId = oxUtilsObject::getInstance()->generateUId();

                $sTemplate = $aValue["template"];
                $iPosition = $aValue["position"] ? $aValue["position"] : 1;
                $sBlock = $aValue["block"];
                $sFile = $aValue["file"];

                $sSql = "INSERT INTO `oxtplblocks` (`OXID`, `OXACTIVE`, `OXSHOPID`, `OXTEMPLATE`, `OXBLOCKNAME`, `OXPOS`, `OXFILE`, `OXMODULE`)
                         VALUES ('{$sOxId}', 1, '{$sShopId}', " . $oDb->quote($sTemplate) . ", " . $oDb->quote($sBlock) . ", " . $oDb->quote($iPosition) . ", " . $oDb->quote($sFile) . ", '{$sModuleId}')";

                $oDb->execute($sSql);
            }
        }
    }

    /**
     * Add module files to config for auto loader.
     *
     * @param array  $aModuleFiles Module files array
     * @param string $sModuleId    Module id
     */
    protected function _addModuleFiles($aModuleFiles, $sModuleId)
    {
        $aFiles = (array) $this->getConfig()->getConfigParam('aModuleFiles');

        if (is_array($aModuleFiles)) {
            $aFiles[$sModuleId] = array_change_key_case($aModuleFiles, CASE_LOWER);
        }

        $this->_saveToConfig('aModuleFiles', $aFiles);
    }

    /**
     * Add module template files to config for smarty.
     *
     * @param array  $aModuleTemplates Module templates array
     * @param string $sModuleId        Module id
     */
    protected function _addTemplateFiles($aModuleTemplates, $sModuleId)
    {
        $aTemplates = (array) $this->getConfig()->getConfigParam('aModuleTemplates');
        if (is_array($aModuleTemplates)) {
            $aTemplates[$sModuleId] = $aModuleTemplates;
        }

        $this->_saveToConfig('aModuleTemplates', $aTemplates);
    }

    /**
     * Add module settings to database.
     *
     * @param array  $aModuleSettings Module settings array
     * @param string $sModuleId       Module id
     */
    protected function _addModuleSettings($aModuleSettings, $sModuleId)
    {
        $this->_removeNotUsedSettings($aModuleSettings, $sModuleId);
        $oConfig = $this->getConfig();
        $sShopId = $oConfig->getShopId();
        $oDb = oxDb::getDb();

        if (is_array($aModuleSettings)) {

            foreach ($aModuleSettings as $aValue) {
                $sOxId = oxUtilsObject::getInstance()->generateUId();

                $sModule = 'module:' . $sModuleId;
                $sName = $aValue["name"];
                $sType = $aValue["type"];
                $sValue = is_null($oConfig->getConfigParam($sName)) ? $aValue["value"] : $oConfig->getConfigParam($sName);
                $sGroup = $aValue["group"];

                $sConstraints = "";
                if ($aValue["constraints"]) {
                    $sConstraints = $aValue["constraints"];
                } elseif ($aValue["constrains"]) {
                    $sConstraints = $aValue["constrains"];
                }

                $iPosition = $aValue["position"] ? $aValue["position"] : 1;

                $oConfig->setConfigParam($sName, $sValue);
                $oConfig->saveShopConfVar($sType, $sName, $sValue, $sShopId, $sModule);

                $sDeleteSql = "DELETE FROM `oxconfigdisplay` WHERE OXCFGMODULE=" . $oDb->quote($sModule) . " AND OXCFGVARNAME=" . $oDb->quote($sName);
                $sInsertSql = "INSERT INTO `oxconfigdisplay` (`OXID`, `OXCFGMODULE`, `OXCFGVARNAME`, `OXGROUPING`, `OXVARCONSTRAINT`, `OXPOS`) " .
                              "VALUES ('{$sOxId}', " . $oDb->quote($sModule) . ", " . $oDb->quote($sName) . ", " . $oDb->quote($sGroup) . ", " . $oDb->quote($sConstraints) . ", " . $oDb->quote($iPosition) . ")";

                $oDb->execute($sDeleteSql);
                $oDb->execute($sInsertSql);
            }
        }
    }

    /**
     * Add module events to config.
     *
     * @param array  $aModuleEvents Module events
     * @param string $sModuleId     Module id
     */
    protected function _addModuleEvents($aModuleEvents, $sModuleId)
    {
        $aEvents = (array) $this->getConfig()->getConfigParam('aModuleEvents');
        if (is_array($aEvents)) {
            $aEvents[$sModuleId] = $aModuleEvents;
        }

        $this->_saveToConfig('aModuleEvents', $aEvents);
    }

    /**
     * Add module version to config.
     *
     * @param string $sModuleVersion Module version
     * @param string $sModuleId      Module id
     */
    protected function _addModuleVersion($sModuleVersion, $sModuleId)
    {
        $aVersions = (array) $this->getConfig()->getConfigParam('aModuleVersions');
        if (is_array($aVersions)) {
            $aVersions[$sModuleId] = $sModuleVersion;
        }

        $this->_saveToConfig('aModuleVersions', $aVersions);
    }

    /**
     * Call module event.
     *
     * @param string $sEvent    Event name
     * @param string $sModuleId Module Id
     */
    protected function _callEvent($sEvent, $sModuleId)
    {
        $aModuleEvents = (array) $this->getConfig()->getConfigParam('aModuleEvents');

        if (isset($aModuleEvents[$sModuleId], $aModuleEvents[$sModuleId][$sEvent])) {
            $mEvent = $aModuleEvents[$sModuleId][$sEvent];

            if (is_callable($mEvent)) {
                call_user_func($mEvent);
            }
        }
    }


    /**
     * Removes garbage ( module not used extensions ) from all installed extensions list
     *
     * @param array    $aInstalledExtensions Installed extensions
     * @param oxModule $oModule              Module
     *
     * @return array
     */
    protected function _removeNotUsedExtensions($aInstalledExtensions, oxModule $oModule)
    {
        $aModuleExtensions = $oModule->getExtensions();

        $aInstalledModuleExtensions = $this->_filterModuleArray($aInstalledExtensions, $oModule->getId());

        if (count($aInstalledModuleExtensions)) {
            $aGarbage = $this->_getModuleExtensionsGarbage($aModuleExtensions, $aInstalledModuleExtensions);

            if (count($aGarbage)) {
                $aInstalledExtensions = $this->_removeGarbage($aInstalledExtensions, $aGarbage);
            }
        }

        return $aInstalledExtensions;
    }

    /**
     * Returns extension which is no longer in metadata - garbage
     *
     * @param array $aModuleMetaDataExtensions  extensions defined in metadata.
     * @param array $aModuleInstalledExtensions extensions which are installed
     *
     * @return array
     */
    protected function _getModuleExtensionsGarbage($aModuleMetaDataExtensions, $aModuleInstalledExtensions)
    {
        $aGarbage = $aModuleInstalledExtensions;

        foreach ($aModuleMetaDataExtensions as $sClassName => $sClassPath) {
            if (isset($aGarbage[$sClassName])) {
                unset($aGarbage[$sClassName][array_search($sClassPath, $aGarbage[$sClassName])]);
                if (count($aGarbage[$sClassName]) == 0) {
                    unset($aGarbage[$sClassName]);
                }
            }
        }

        return $aGarbage;
    }

    /**
     * Removes garbage - not exiting module extensions, returns clean array of installed extensions
     *
     * @param array $aInstalledExtensions all installed extensions ( from all modules )
     * @param array $aGarbage             extension which are not used and should be removed
     *
     * @return array
     */
    protected function _removeGarbage($aInstalledExtensions, $aGarbage)
    {
        foreach ($aGarbage as $sClassName => $aClassPaths) {
            foreach ($aClassPaths as $sClassPath) {
                if (isset($aInstalledExtensions[$sClassName])) {
                    unset($aInstalledExtensions[$sClassName][array_search($sClassPath, $aInstalledExtensions[$sClassName])]);
                    if (count($aInstalledExtensions[$sClassName]) == 0) {
                        unset($aInstalledExtensions[$sClassName]);
                    }
                }
            }
        }

        return $aInstalledExtensions;
    }

    /**
     * Removes configs which are removed from module metadata
     *
     * @param array  $aModuleSettings Module settings
     * @param string $sModuleId       Module id
     */
    protected function _removeNotUsedSettings($aModuleSettings, $sModuleId)
    {
        $aModuleConfigs = $this->_getModuleConfigs($sModuleId);
        $aModuleSettings = $this->_parseModuleSettings($aModuleSettings);

        $aConfigsToRemove = array_diff($aModuleConfigs, $aModuleSettings);
        if (!empty($aConfigsToRemove)) {
            $this->_removeModuleConfigs($sModuleId, $aConfigsToRemove);
        }
    }

    /**
     * Returns module configuration from database
     *
     * @param string $sModuleId Module id
     *
     * @return array
     */
    protected function _getModuleConfigs($sModuleId)
    {
        $oDb = oxDb::getDb();
        $sQuotedShopId = $oDb->quote($this->getConfig()->getShopId());
        $sQuotedModuleId = $oDb->quote('module:' . $sModuleId);

        $sModuleConfigsQuery = "SELECT oxvarname FROM oxconfig WHERE oxmodule = $sQuotedModuleId AND oxshopid = $sQuotedShopId";

        return $oDb->getCol($sModuleConfigsQuery);
    }

    /**
     * Parses module config variable names to array from module settings
     *
     * @param array $aModuleSettings Module settings
     *
     * @return array
     */
    protected function _parseModuleSettings($aModuleSettings)
    {
        $aSettings = array();

        if (is_array($aModuleSettings)) {
            foreach ($aModuleSettings as $aSetting) {
                $aSettings[] = $aSetting['name'];
            }
        }

        return $aSettings;
    }

    /**
     * Removes module configs from database
     *
     * @param string $sModuleId        Module id
     * @param array  $aConfigsToRemove Configs to remove
     */
    protected function _removeModuleConfigs($sModuleId, $aConfigsToRemove)
    {
        $oDb = oxDb::getDb();
        $sQuotedShopId = $oDb->quote($this->getConfig()->getShopId());
        $sQuotedModuleId = $oDb->quote('module:' . $sModuleId);

        $aQuotedConfigsToRemove = array_map(array($oDb, 'quote'), $aConfigsToRemove);
        $sDeleteSql = "DELETE
                       FROM `oxconfig`
                       WHERE oxmodule = $sQuotedModuleId AND
                             oxshopid = $sQuotedShopId AND
                             oxvarname IN (" . implode(", ", $aQuotedConfigsToRemove) . ")";

        $oDb->execute($sDeleteSql);
    }

    /**
     * Filter module array using module id
     *
     * @param array  $aModules  Module array (nested format)
     * @param string $sModuleId Module id/folder name
     *
     * @return array
     */
    protected function _filterModuleArray($aModules, $sModuleId)
    {
        $aFilteredModules = array();
        foreach ($aModules as $sClass => $aExtend) {
            foreach ($aExtend as $sExtendPath) {
                if (strpos($sExtendPath, $sModuleId . "/") === 0) {
                    $aFilteredModules[$sClass][] = $sExtendPath;
                }
            }
        }

        return $aFilteredModules;
    }

    /**
     * Save module parameters to shop config
     *
     * @param string $sVariableName  config name
     * @param string $sVariableValue config value
     * @param string $sVariableType  config type
     */
    protected function _saveToConfig($sVariableName, $sVariableValue, $sVariableType = 'aarr')
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam($sVariableName, $sVariableValue);
        $oConfig->saveShopConfVar($sVariableType, $sVariableName, $sVariableValue);
    }
}
