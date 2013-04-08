<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: $
 */

/**
 * Class handling shop modules
 *
 */
class oxModule extends oxSuperCfg
{
    /**
     * Modules info array
     *
     * @var array
     */
    protected $_aModule = array();

    /**
     * Defines if module has metadata file or not
     *
     * @var bool
     */
    protected $_blMetadata   = false;

    /**
     * Defines if module is registered in metadata or legacy storage
     *
     * @var bool
     */
    protected $_blRegistered = false;

    /**
     * Defines if it is a single file legacy extension
     *
     * @var bool
     */
    protected $_blFile       = false;

    /**
     * Defines if it is a legacy extension
     *
     * @var bool
     */
    protected $_blLegacy     = false;

    /**
     * Set passed module data
     *
     * @param array $aModule module data
     *
     * @return null
     */
    public function setModuleData( $aModule )
    {
        $this->_aModule = $aModule;
    }

    /**
     * Load module info
     *
     * @param string $sModuleId Module ID
     *
     * @return bool
     */
    public function load( $sModuleId )
    {
        if ( $this->loadModule($sModuleId) ) return true;

        if ( $this->loadLegacyModule($sModuleId) ) return true;

        if ( $this->loadUnregisteredModule($sModuleId) ) return true;

        return false;
    }

    /**
     * Load module by dir name
     *
     * @param string $sModuleDir Module dir name
     *
     * @return bool
     */
    public function loadByDir( $sModuleDir )
    {
        $sModuleId    = null;
        $aModulePaths = $this->getModulePaths();

        if ( is_array($aModulePaths) ) {
            $sModuleId = array_search( $sModuleDir, $aModulePaths);
        }

        // if no module id defined, using module dir as id
        if ( !$sModuleId ) {
            $sModuleId = $sModuleDir;
        }

        return $this->load( $sModuleId );
    }

    /**
     * Load Extension from metadata
     *
     * @param string $sModuleId Module ID
     *
     * @return bool
     */
    public function loadModule( $sModuleId )
    {
        $sModuleDir = $this->getModulePath( $sModuleId );

        $sFilePath = $this->getConfig()->getModulesDir() . $sModuleDir . "/metadata.php";
        if ( file_exists( $sFilePath ) && is_readable( $sFilePath ) ) {
            $aModule = array();
            include $sFilePath;
            $this->_aModule = $aModule;
            $this->_blLegacy      = false;
            $this->_blRegistered  = true;
            $this->_blMetadata    = true;
            $this->_blFile        = false;
            $this->_aModule['active'] = $this->isActive() || !$this->isExtended();
            return true;
        }
        return false;
    }

    /**
     * Load Extension from legacy metadata
     *
     * @param string $sModuleId Module ID
     *
     * @return bool
     */
    public function loadLegacyModule( $sModuleId )
    {
        $aLegacyModules = $this->getLegacyModules();
        $sModuleDir = $this->getModulePath( $sModuleId );

        // registered legacy module
        if ( isset( $aLegacyModules[$sModuleId] ) ) {
            $this->_aModule = $aLegacyModules[$sModuleId];
            $this->_blLegacy      = true;
            $this->_blRegistered  = true;
            $this->_blMetadata    = false;
            $this->_blFile        = empty( $sModuleDir );
            $this->_aModule['active'] = $this->isActive();
            return true;
        }
        return false;
    }

    /**
     * Load extension without any metadata
     *
     * @param string $sModuleId Module ID
     *
     * @return bool
     */
    public function loadUnregisteredModule( $sModuleId )
    {
        $oConfig = $this->getConfig();
        $aModules = $this->getAllModules();

        $sModuleDir = $this->getModulePath( $sModuleId );

        $sFilePath = $oConfig->getModulesDir() . $sModuleDir ;
        if ( file_exists( $sFilePath ) && is_readable( $sFilePath ) ) {
            $this->_aModule = array();
            $this->_aModule['id'] = $sModuleId;
            $this->_aModule['title'] = $sModuleId;
            $this->_aModule['extend'] = $this->buildModuleChains($this->filterModuleArray($aModules, $sModuleId));
            $this->_blLegacy      = true;
            $this->_blRegistered  = false;
            $this->_blMetadata    = false;
            $this->_blFile        = !is_dir($oConfig->getModulesDir() . $sModuleId);
            $this->_aModule['active'] = $this->isActive();
            return true;
        }
        return false;
    }

    /**
     * Get module description
     *
     * @return string
     */
    public function getDescription()
    {
        $iLang = oxRegistry::getLang()->getTplLanguage();

        return $this->getInfo( "description", $iLang );
    }

    /**
     * Get module title
     *
     * @return string
     */
    public function getTitle()
    {
        $iLang = oxRegistry::getLang()->getTplLanguage();

        return $this->getInfo( "title", $iLang );
    }

    /**
     * Get module ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->_aModule['id'];
    }

    /**
     * Get module ID
     *
     * @param string $sModule extension full path
     *
     * @return string
     */
    public function getIdByPath($sModule)
    {
        $myConfig     = $this->getConfig();
        $aModulePaths = $myConfig->getConfigParam( 'aModulePaths' );
        $sModuleId    = null;
        if (is_array( $aModulePaths )) {
            foreach ($aModulePaths as $sId => $sPath) {
                if (strpos($sModule, $sPath."/") === 0 ) {
                    $sModuleId = $sId;
                }
            }
        }
        if (!$sModuleId) {
            $sModuleId = substr( $sModule, 0, strpos( $sModule, "/" ) );
        }
        if (!$sModuleId) {
            $sModuleId = $sModule;
        }
        return $sModuleId;
    }

    /**
     * Get module info item. If second param is passed, will try
     * to get value according selected language.
     *
     * @param string $sName name of info item to retrieve
     * @param string $iLang language ID
     *
     * @return mixed
     */
    public function getInfo( $sName, $iLang = null )
    {
        if (isset($this->_aModule[$sName])) {

            if ( $iLang !== null && is_array($this->_aModule[$sName]) ) {
                $sValue = null;

                $sLang = oxRegistry::getLang()->getLanguageAbbr( $iLang );

                if ( !empty($this->_aModule[$sName]) ) {
                    if ( !empty( $this->_aModule[$sName][$sLang] ) ) {
                        $sValue = $this->_aModule[$sName][$sLang];
                    } elseif ( !empty($this->_aModule['lang']) ) {
                        // trying to get value according default language
                        $sValue = $this->_aModule[$sName][$this->_aModule['lang']];
                    } else {
                        // returning first array value
                        $sValue = reset( $this->_aModule[$sName] );
                    }

                    return $sValue;
                }
            } else {
                return $this->_aModule[$sName];
            }
        }
    }

    /**
     * Check if extension is active
     *
     * @return bool
     */
    public function isActive()
    {
        $blActive = false;
        $sId = $this->getId();
        if (isset($sId)) {
            if ( is_array($this->_aModule['extend']) && !empty($this->_aModule['extend']) ) {
                $aAddModules = $this->_aModule['extend'];
                $aInstalledModules = $this->getAllModules();
                $iClCount = count($aAddModules);
                $iActive  = 0;

                foreach ($aAddModules as $sClass => $sModule) {
                    if ( (isset($aInstalledModules[$sClass]) && in_array($sModule, $aInstalledModules[$sClass])) ) {
                        $iActive ++;
                    }
                }
                $blActive = $iClCount > 0 && $iActive == $iClCount;

                $aDisabledModules = $this->getDisabledModules();
                if ( $blActive && ( is_array($aDisabledModules) && in_array($sId, $aDisabledModules) ) ) {
                    $blActive = false;
                }
            } else {
                //handling modules that does not extend any class
                $aDisabledModules = $this->getDisabledModules();
                if ( is_array($aDisabledModules) && !in_array($sId, $aDisabledModules) ) {
                    $blActive = true;
                }
            }
        }

        return $blActive;
    }

    /**
     * Check if extension das any extended classes
     *
     * @return bool
     */
    public function isExtended()
    {
        if ($this->hasMetadata() && !empty($this->_aModule['extend'])) {
            return true;
        }

        return false;
    }

    /**
     * Checks if module is defined as legacy module
     *
     * @return bool
     */
    public function isLegacy()
    {
        return $this->_blLegacy;
    }

    /**
     * Checks if module is registered in any way
     *
     * @return bool
     */
    public function isRegistered()
    {
        return $this->_blRegistered;
    }

    /**
     * Checks if module has metadata
     *
     * @return bool
     */
    public function hasMetadata()
    {
        return $this->_blMetadata;
    }

    /**
     * Checks if module is single file
     *
     * @return bool
     */
    public function isFile()
    {
        return $this->_blFile;
    }

    /**
     * Activate extension by merging module class inheritance information with shop module array
     *
     * @return bool
     */
    public function activate()
    {
        if (isset($this->_aModule['extend']) && is_array($this->_aModule['extend'])) {
            $oConfig     = oxRegistry::getConfig();
            $aAddModules = $this->_aModule['extend'];
            $sModuleId   = $this->getId();

            $aInstalledModules = $this->getAllModules();
            $aDisabledModules  = $this->getDisabledModules();

            $aModules = $this->mergeModuleArrays($aInstalledModules, $aAddModules);
            $aModules = $this->buildModuleChains($aModules);

            $oConfig->setConfigParam('aModules', $aModules);
            $oConfig->saveShopConfVar('aarr', 'aModules', $aModules);

            if ( isset($aDisabledModules) && is_array($aDisabledModules) ) {
                $aDisabledModules = array_diff($aDisabledModules, array($sModuleId));
                $oConfig->setConfigParam('aDisabledModules', $aDisabledModules);
                $oConfig->saveShopConfVar('arr', 'aDisabledModules', $aDisabledModules);
            }

            // checking if module has tpl blocks and they are installed
            if ( !$this->_hasInstalledTemplateBlocks($sModuleId) ) {
                // installing module blocks
                $this->_addTemplateBlocks( $this->getInfo("blocks") );
            } else {
                //activate oxblocks
                $this->_changeBlockStatus( $sModuleId, "1" );
            }

            // Register new module templates
            $this->_addModuleFiles($this->getInfo("files") );

            // Register new module templates
            $this->_addTemplateFiles($this->getInfo("templates") );

            // Add module settings
            $this->_addModuleSettings($this->getInfo("settings"));

            // Add module version
            $this->_addModuleVersion($this->getInfo("version"));

            // Add module events
            $this->_addModuleEvents($this->getInfo("events"));

            //resets cache
            $this->_resetCache();


            $this->_callEvent('onActivate', $sModuleId);

            return true;
        }
        return false;
    }

    /**
     * Deactivate extension by adding disable module class information to disabled module array
     *
     * @param string $sModuleId Module Id
     *
     * @return bool
     */
    public function deactivate($sModuleId = null)
    {
        $oConfig = $this->getConfig();
        if (!isset($sModuleId)) {
            $sModuleId = $this->getId();
        }
        if (isset($sModuleId)) {

            $this->_callEvent('onDeactivate', $sModuleId);

            $aDisabledModules = $this->getDisabledModules();

            if (!is_array($aDisabledModules)) {
                $aDisabledModules = array();
            }
            $aModules = array_merge($aDisabledModules, array($sModuleId));
            $aModules = array_unique($aModules);

            $oConfig->saveShopConfVar('arr', 'aDisabledModules', $aModules);
            $oConfig->setConfigParam('aDisabledModules', $aModules);

            //deactivate oxblocks too
            $this->_changeBlockStatus( $sModuleId );

            //resets cache
            $this->_resetCache();


            return true;
        }
        return false;
    }

    /**
     * Call module event.
     *
     * @param string $sEvent    Event name
     * @param string $sModuleId Module Id
     *
     * @return null
     */
    protected function _callEvent($sEvent, $sModuleId)
    {
        $aModuleEvents = $this->getModuleEvents();

        if (isset($aModuleEvents[$sModuleId], $aModuleEvents[$sModuleId][$sEvent])) {
            $mEvent = $aModuleEvents[$sModuleId][$sEvent];

            if (is_callable($mEvent)) {
                call_user_func($mEvent);
            }
        }
    }

    /**
     * Deactivates or activates oxblocks of a module
     *
     * @param string  $sModule Module name
     * @param integer $iStatus 0 or 1 to (de)activate blocks
     *
     * @return null
     */
    protected function _changeBlockStatus( $sModule, $iStatus = 0 )
    {
        $oDb = oxDb::getDb();
        $sShopId   = $this->getConfig()->getShopId();
        $oDb->execute("UPDATE oxtplblocks SET oxactive = '".(int) $iStatus."' WHERE oxmodule =". $oDb->quote($sModule)."AND oxshopid = '$sShopId'");
    }

    /**
     * Resets template, language and menu xml cache
     *
     * @return null
     */
    protected function _resetCache()
    {
        $aTemplates = $this->getTemplates();
        $oUtils = oxRegistry::getUtils();
        $oUtils->resetTemplateCache($aTemplates);
        $oUtils->resetLanguageCache();
        $oUtils->resetMenuCache();

        $oUtilsObject = oxUtilsObject::getInstance();
        $oUtilsObject->resetModuleVars();
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
     * Merge two nested module arrays together so that the values of
     * $aAddModuleArray are appended to the end of the $aAllModuleArray
     *
     * @param array $aAllModuleArray All Module array (nested format)
     * @param array $aAddModuleArray Added Module array (nested format)
     *
     * @return array
     */
    public function mergeModuleArrays($aAllModuleArray, $aAddModuleArray)
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
     * Filter module array using module id
     *
     * @param array  $aModules  Module array (nested format)
     * @param string $sModuleId Module id/folder name
     *
     * @return array
     */
    public function filterModuleArray($aModules, $sModuleId)
    {
        $aFilteredModules = array();
        foreach ($aModules as $sClass => $aExtend) {
            foreach ($aExtend as $sExtendPath) {
                if (strstr($sExtendPath, $sModuleId.'/')) {
                    $aFilteredModules[$sClass][] = $sExtendPath;
                }
            }
        }
        return $aFilteredModules;
    }

    /**
     * Get module dir
     *
     * @param string $sModuleId Module ID
     *
     * @return string
     */
    public function getModulePath( $sModuleId = null )
    {
        if ( !$sModuleId ) {
            $sModuleId = $this->getId();
        }

        $aModulePaths = $this->getModulePaths();

        $sModulePath = $aModulePaths[$sModuleId];

        // if still no module dir, try using module ID as dir name
        if ( !$sModulePath && is_dir($this->getConfig()->getModulesDir().$sModuleId) ) {
            $sModulePath = $sModuleId;
        }

        return $sModulePath;
    }

    /**
     * Get parsed modules
     *
     * @return array
     */
    public function getAllModules()
    {
        return $this->getConfig()->getAllModules();
    }

    /**
     * Get legacy modules list
     *
     * @return array
     */
    public function getLegacyModules()
    {
        return $this->getConfig()->getConfigParam('aLegacyModules');
    }

    /**
     * Get disabled module id's
     *
     * @return array
     */
    public function getDisabledModules()
    {
        return $this->getConfig()->getConfigParam('aDisabledModules');
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
     * Get module template files
     *
     * @return array
     */
    public function getModuleTemplates()
    {
        return (array) $this->getConfig()->getConfigParam('aModuleTemplates');
    }

    /**
     * Get module files
     *
     * @return array
     */
    public function getModuleFiles()
    {
        return (array) $this->getConfig()->getConfigParam('aModuleFiles');
    }

    /**
     * Get module versions
     *
     * @return array
     */
    public function getModuleVersions()
    {
        return (array) $this->getConfig()->getConfigParam('aModuleVersions');
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
     * Checks if module has installed template blocks
     *
     * @param string $sModuleId Module ID
     *
     * @return bool
     */
    protected function _hasInstalledTemplateBlocks( $sModuleId )
    {
        $sShopId   = $this->getConfig()->getShopId();
        $oDb = oxDb::getDb();
        $blRes = $oDb->getOne( "SELECT 1 FROM oxtplblocks WHERE oxmodule = ".$oDb->quote($sModuleId)." AND oxshopid = '$sShopId' LIMIT 1" );
        return (bool) $blRes;
    }

    /**
     * Add module templates to database.
     *
     * @param array  $aModuleBlocks Module blocks array
     * @param string $sModuleId     Module id
     *
     * @return null
     */
    protected function _addTemplateBlocks( $aModuleBlocks, $sModuleId = null )
    {
        if (is_null($sModuleId)) {
            $sModuleId = $this->getId();
        }

        $sShopId = $this->getConfig()->getShopId();
        $oDb     = oxDb::getDb();

        if ( is_array($aModuleBlocks) ) {

            foreach ( $aModuleBlocks as $aValue ) {
                $sOxId = oxUtilsObject::getInstance()->generateUId();

                $sTemplate = $aValue["template"];
                $iPosition = $aValue["position"]?$aValue["position"]:1;
                $sBlock    = $aValue["block"];
                $sFile     = $aValue["file"];

                $sSql = "INSERT INTO `oxtplblocks` (`OXID`, `OXACTIVE`, `OXSHOPID`, `OXTEMPLATE`, `OXBLOCKNAME`, `OXPOS`, `OXFILE`, `OXMODULE`)
                         VALUES ('{$sOxId}', 1, '{$sShopId}', ".$oDb->quote($sTemplate).", ".$oDb->quote($sBlock).", ".$oDb->quote($iPosition).", ".$oDb->quote($sFile).", '{$sModuleId}')";

                $oDb->execute( $sSql );
            }
        }
    }

    /**
     * Add module template files to config for smarty.
     *
     * @param array  $aModuleTemplates Module templates array
     * @param string $sModuleId        Module id
     *
     * @return null
     */
    protected function _addTemplateFiles( $aModuleTemplates , $sModuleId = null)
    {
        if (is_null($sModuleId)) {
            $sModuleId = $this->getId();
        }

        $oConfig    = $this->getConfig();
        $aTemplates = $this->getModuleTemplates();
        if ( is_array($aModuleTemplates) ) {
            $aTemplates[$sModuleId] = $aModuleTemplates;
        }

        $oConfig->setConfigParam('aModuleTemplates', $aTemplates);
        $oConfig->saveShopConfVar('aarr', 'aModuleTemplates', $aTemplates);
    }

    /**
     * Add module version to config.
     *
     * @param string $sModuleVersion Module version
     * @param string $sModuleId      Module id
     *
     * @return null
     */
    protected function _addModuleVersion( $sModuleVersion, $sModuleId = null)
    {
        if (is_null($sModuleId)) {
            $sModuleId = $this->getId();
        }

        $oConfig = $this->getConfig();
        $aVersions  = $this->getModuleVersions();
        if ( is_array($aVersions) ) {
            $aVersions[$sModuleId] = $sModuleVersion;
        }

        $oConfig->setConfigParam('aModuleVersions', $aVersions);
        $oConfig->saveShopConfVar('aarr', 'aModuleVersions', $aVersions);
    }

    /**
     * Add module events to config.
     *
     * @param array  $aModuleEvents Module events
     * @param string $sModuleId     Module id
     *
     * @return null
     */
    protected function _addModuleEvents( $aModuleEvents, $sModuleId = null)
    {
        if (is_null($sModuleId)) {
            $sModuleId = $this->getId();
        }

        $oConfig = $this->getConfig();
        $aEvents  = $this->getModuleEvents();
        if ( is_array($aEvents) ) {
            $aEvents[$sModuleId] = $aModuleEvents;
        }

        $oConfig->setConfigParam('aModuleEvents', $aEvents);
        $oConfig->saveShopConfVar('aarr', 'aModuleEvents', $aEvents);
    }

    /**
     * Add module files to config for auto loader.
     *
     * @param array  $aModuleFiles Module files array
     * @param string $sModuleId    Module id
     *
     * @return null
     */
    protected function _addModuleFiles( $aModuleFiles, $sModuleId = null)
    {
        if (is_null($sModuleId)) {
            $sModuleId = $this->getId();
        }

        $oConfig = $this->getConfig();
        $aFiles  = $this->getModuleFiles();
        if ( is_array($aModuleFiles) ) {
            $aFiles[$sModuleId] = array_change_key_case($aModuleFiles, CASE_LOWER);
        }

        $oConfig->setConfigParam('aModuleFiles', $aFiles);
        $oConfig->saveShopConfVar('aarr', 'aModuleFiles', $aFiles);
    }

    /**
     * Add module settings to database.
     *
     * @param array  $aModuleSettings Module settings array
     * @param string $sModuleId       Module id
     *
     * @return null
     */
    protected function _addModuleSettings( $aModuleSettings, $sModuleId = null )
    {
        if (is_null($sModuleId)) {
            $sModuleId = $this->getId();
        }
        $oConfig = $this->getConfig();
        $sShopId = $oConfig->getShopId();
        $oDb     = oxDb::getDb();

        if ( is_array($aModuleSettings) ) {

            foreach ( $aModuleSettings as $aValue ) {
                $sOxId = oxUtilsObject::getInstance()->generateUId();

                $sModule     = 'module:'.$sModuleId;
                $sName       = $aValue["name"];
                $sType       = $aValue["type"];
                $sValue      = is_null($oConfig->getConfigParam($sName))?$aValue["value"]:$oConfig->getConfigParam($sName);
                $sGroup      = $aValue["group"];

                $sConstraints = "";
                if ( $aValue["constraints"] ) {
                    $sConstraints = $aValue["constraints"];
                } elseif ( $aValue["constrains"] ) {
                    $sConstraints = $aValue["constrains"];
                }

                $iPosition   = $aValue["position"]?$aValue["position"]:1;

                $oConfig->setConfigParam($sName, $sValue);
                $oConfig->saveShopConfVar($sType, $sName, $sValue, $sShopId, $sModule);

                $sDeleteSql = "DELETE FROM `oxconfigdisplay` WHERE OXCFGMODULE=".$oDb->quote($sModule)." AND OXCFGVARNAME=".$oDb->quote($sName);
                $sInsertSql = "INSERT INTO `oxconfigdisplay` (`OXID`, `OXCFGMODULE`, `OXCFGVARNAME`, `OXGROUPING`, `OXVARCONSTRAINT`, `OXPOS`) ".
                              "VALUES ('{$sOxId}', ".$oDb->quote($sModule).", ".$oDb->quote($sName).", ".$oDb->quote($sGroup).", ".$oDb->quote($sConstraints).", ".$oDb->quote($iPosition).")";

                $oDb->execute( $sDeleteSql );
                $oDb->execute( $sInsertSql );
            }
        }
    }

    /**
     * Return templates affected by template blocks for given module id.
     *
     * @param string $sModuleId Module id
     *
     * @return array
     */
    public function getTemplates( $sModuleId = null )
    {
        if (is_null($sModuleId)) {
            $sModuleId = $this->getId();
        }

        if (!$sModuleId) {
            return array();
        }

        $sShopId   = $this->getConfig()->getShopId();

        $aTemplates = oxDb::getDb()->getCol("SELECT oxtemplate FROM oxtplblocks WHERE oxmodule = '$sModuleId' AND oxshopid = '$sShopId'" );

        return $aTemplates;
    }

    /**
     * Enables modules, that don't have metadata file activation/deactivation.
     * Writes to "aLegacyModules" config variable classes, that current module
     * extends.
     *
     * @param string $sModuleId   Module id
     * @param string $sModuleName Module name
     * @param string $aModuleInfo Extended classes
     *
     * @return string module id
     */
    public function saveLegacyModule($sModuleId, $sModuleName, $aModuleInfo = null)
    {
        $aLegacyModules = $this->getLegacyModules();

        if ( !empty( $aModuleInfo ) && is_array($aModuleInfo)) {
            $aLegacyModules[$sModuleId]["id"] = $sModuleId;
            $aLegacyModules[$sModuleId]["title"] = ( $sModuleName ) ? $sModuleName : $sModuleId;
            $aLegacyModules[$sModuleId]['extend'] = array();

            foreach ( $aModuleInfo as $sKey => $sValue ) {
                if ( strpos( $sValue, "=>" ) > 1 ) {
                    $aClassInfo    = explode( "=>", $sValue );
                    $sClassName    = trim( $aClassInfo[0] );
                    $sExtendString = trim( $aClassInfo[1] );
                    $aLegacyModules[$sModuleId]['extend'][$sClassName] = $sExtendString;
                }
            }
        }

        if ( !empty( $aLegacyModules[$sModuleId]['extend'] ) ) {
            $this->getConfig()->saveShopConfVar( "aarr", "aLegacyModules", $aLegacyModules );
        }
        return $sModuleId;
    }

    /**
     * Update module ID in modules config variables aModulePaths and aDisabledModules.
     *
     * @param string $sModuleLegacyId Old module ID
     * @param string $sModuleId       New module ID
     *
     * @return null
     */
    public function updateModuleIds( $sModuleLegacyId, $sModuleId )
    {
        $oConfig = $this->getConfig();

        // updating module ID in aModulePaths config var
        $aModulePaths = $oConfig->getConfigParam( 'aModulePaths' );
        $aModulePaths[$sModuleId] = $aModulePaths[$sModuleLegacyId];
        unset( $aModulePaths[$sModuleLegacyId] );

        $oConfig->saveShopConfVar( 'aarr', 'aModulePaths', $aModulePaths );

        if ( isset($aModulePaths[$sModuleLegacyId]) ) {
            $aModulePaths[$sModuleId] = $aModulePaths[$sModuleLegacyId];
            unset( $aModulePaths[$sModuleLegacyId] );
            $oConfig->saveShopConfVar( 'aarr', 'aModulePaths', $aModulePaths );
        }

        // updating module ID in aDisabledModules config var
        $aDisabledModules = $oConfig->getConfigParam( 'aDisabledModules' );

        if ( is_array($aDisabledModules) ) {
            $iOldKey = array_search( $sModuleLegacyId, $aDisabledModules );
            if ( $iOldKey !== false ) {
                unset( $aDisabledModules[$iOldKey] );
                $aDisabledModules[$iOldKey] = $sModuleId;
                $oConfig->saveShopConfVar( 'arr', 'aDisabledModules', $aDisabledModules );
            }
        }
    }
}
