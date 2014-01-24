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
 * @copyright (C) OXID eSales AG 2003-2014
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
        $sModulePath = $this->getModuleFullPath( $sModuleId );
        $sMetadataPath = $sModulePath . "/metadata.php";

        if ( $sModulePath && file_exists( $sMetadataPath ) && is_readable( $sMetadataPath ) ) {
            $aModule = array();
            include $sMetadataPath;
            $this->_aModule = $aModule;
            $this->_blLegacy      = false;
            $this->_blRegistered  = true;
            $this->_blMetadata    = true;
            $this->_blFile        = false;
            $this->_aModule['active'] = $this->isActive();
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
        $sModulePath = $this->getModuleFullPath( $sModuleId );

        if ( !$sModulePath ) {
            $sModulePath = $this->getConfig()->getModulesDir() . $sModuleId;
        }

        if ( file_exists( $sModulePath ) && is_readable( $sModulePath ) ) {
            $aModules = $this->getModulesWithExtendedClass();

            $this->_aModule = array();
            $this->_aModule['id'] = $sModuleId;
            $this->_aModule['title'] = $sModuleId;
            $this->_aModule['extend'] = $this->buildModuleChains( $this->filterModuleArray( $aModules, $sModuleId ) );
            $this->_blLegacy      = true;
            $this->_blRegistered  = false;
            $this->_blMetadata    = false;
            $this->_blFile        = !is_dir($this->getConfig()->getModulesDir() . $sModuleId );
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
     * Returns array of module extensions
     *
     * @return array
     */
    public function getExtensions()
    {
        return isset( $this->_aModule['extend'] ) ? $this->_aModule['extend'] : array();
    }

    /**
     * Get module ID
     *
     * @param string $sModule extension full path
     *
     * @return string
     */
    public function getIdByPath( $sModule )
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
                    } elseif ( !empty( $this->_aModule['lang'] ) ) {
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
        if ( !is_null( $sId ) ) {
            if ( $this->hasExtendClass() ) {
                    $blActive = $this->_isExtensionsActive();
                if ( $blActive && $this->_isInDisabledList( $sId ) ) {
                    $blActive = false;
                }
            } else {
                $aDisabledModules = $this->getDisabledModules();
                if ( is_array( $aDisabledModules ) && !in_array( $sId, $aDisabledModules ) ) {
                    $blActive = true;
                }
            }
        }

        return $blActive;
    }


    /**
     * Check if extension has any extended classes
     *
     * @deprecated since v5.1.2 (2013-12-10); Naming changed use function hasExtendClass().
     * @deprecated use together with hasMetadata if needed.
     *
     * @return bool
     */
    public function isExtended()
    {
        if ( $this->hasMetadata() && $this->hasExtendClass() ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasExtendClass()
    {
        $aExtensions = $this->getExtensions();
        return isset( $aExtensions )
            && is_array( $aExtensions )
            && !empty( $aExtensions );
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
        $blResult = false;

        if ( $this->hasMetadata() || $this->hasExtendClass() ) {

            $this->_addExtensions();

            $this->_removeFromDisabledList();

            $this->_addTemplateBlocks( $this->getInfo("blocks") );

            // Register new module files
            $this->_addModuleFiles($this->getInfo("files") );

            // Register new module templates
            $this->_addTemplateFiles( $this->getInfo("templates") );

            // Add module settings
            $this->_addModuleSettings($this->getInfo("settings"));

            // Add module version
            $this->_addModuleVersion($this->getInfo("version"));

            // Add module events
            $this->_addModuleEvents($this->getInfo("events"));

            //resets cache
            $this->_resetCache();


            $this->_callEvent('onActivate',  $this->getId() );

            $blResult = true;
        }
        return $blResult;
    }

    /**
     * Deactivate extension by adding disable module class information to disabled module array
     *
     * @param string $sModuleId Module Id
     *
     * @return bool
     */
    public function deactivate( $sModuleId = null )
    {
        $blResult = false;
        if ( is_null( $sModuleId ) ) {
            $sModuleId = $this->getId();
        }
        if ( isset( $sModuleId ) ) {

            $this->_addToDisabledList( $sModuleId );

            $this->_callEvent( 'onDeactivate', $sModuleId );

            //resets cache
            $this->_resetCache();

            $this->_deleteBlock( $sModuleId );

            $this->_deleteTemplateFiles( $sModuleId );


            $blResult = true;
        }
        return $blResult;
    }

    /**
     * Call module event.
     *
     * @param string $sEvent    Event name
     * @param string $sModuleId Module Id
     *
     * @return null
     */
    protected function _callEvent( $sEvent, $sModuleId )
    {
        $aModuleEvents = $this->getModuleEvents();

        if ( isset( $aModuleEvents[$sModuleId], $aModuleEvents[$sModuleId][$sEvent] ) ) {
            $mEvent = $aModuleEvents[$sModuleId][$sEvent];

            if ( is_callable( $mEvent ) ) {
                call_user_func($mEvent);
            }
        }
    }

    /**
     * Deactivates or activates oxBlocks of a module
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
        $oDb->execute( "UPDATE oxtplblocks SET oxactive = '".(int) $iStatus."' WHERE oxmodule =". $oDb->quote( $sModule )."AND oxshopid = '$sShopId'" );
    }

    /**
     * Deactivates or activates oxBlocks of a module
     *
     * @param string  $sModule Module name
     *
     * @return null
     */
    protected function _deleteBlock( $sModule )
    {
        $oDb = oxDb::getDb();
        $sShopId = $this->getConfig()->getShopId();
        $oDb->execute( "DELETE FROM `oxtplblocks` WHERE `oxmodule` =" . $oDb->quote( $sModule ) . " AND `oxshopid` = " . $oDb->quote( $sShopId ) );
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

        $this->_clearApcCache();
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
        if ( is_array( $aAllModuleArray ) && is_array( $aAddModuleArray ) ) {
            foreach ( $aAddModuleArray as $sClass => $aModuleChain ) {
                if ( !is_array( $aModuleChain ) ) {
                    $aModuleChain = array( $aModuleChain );
                }
                if ( isset( $aAllModuleArray[$sClass] ) ) {
                    foreach ( $aModuleChain as $sModule ) {
                        if ( !in_array( $sModule, $aAllModuleArray[$sClass] ) ) {
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
    public function filterModuleArray( $aModules, $sModuleId )
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
     * Returns full module path
     *
     * @param string $sModuleId
     *
     * @return string
     */
    public function getModuleFullPath( $sModuleId )
    {
        if ( $sModuleDir = $this->getModulePath( $sModuleId ) ) {
            return $this->getConfig()->getModulesDir() . $sModuleDir;
        }
        return false;
    }

    /**
     * Get parsed modules
     *
     * @deprecated since v5.1.2 (2013-12-10); Naming changed use function getModulesWithExtendedClass().
     *
     * @return array
     */
    public function getAllModules()
    {
        return $this->getModulesWithExtendedClass();
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

        $aTemplates = $this->getModuleTemplates();
        if ( is_array($aModuleTemplates) ) {
            $aTemplates[$sModuleId] = $aModuleTemplates;
        }

        $this->_saveToConfig( 'aModuleTemplates', $aTemplates );
    }

    /**
     * Add module template files to config for smarty.
     *
     * @param string $sModuleId        Module id
     *
     * @return null
     */
    protected function _deleteTemplateFiles( $sModuleId = null )
    {
        if (is_null($sModuleId)) {
            $sModuleId = $this->getId();
        }

        $aTemplates = $this->getModuleTemplates();
        unset( $aTemplates[$sModuleId] );

        $this->_saveToConfig( 'aModuleTemplates', $aTemplates );
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

        $aVersions  = $this->getModuleVersions();
        if ( is_array($aVersions) ) {
            $aVersions[$sModuleId] = $sModuleVersion;
        }

        $this->_saveToConfig( 'aModuleVersions', $aVersions );
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

        $aEvents  = $this->getModuleEvents();
        if ( is_array($aEvents) ) {
            $aEvents[$sModuleId] = $aModuleEvents;
        }

        $this->_saveToConfig( 'aModuleEvents', $aEvents );
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

        $aFiles  = $this->getModuleFiles();
        if ( is_array($aModuleFiles) ) {
            $aFiles[$sModuleId] = array_change_key_case($aModuleFiles, CASE_LOWER);
        }

        $this->_saveToConfig( 'aModuleFiles', $aFiles );
    }

    /**
     * Save module parameters to shop config
     *
     * @param string $sVariableName config name
     * @param string $sVariableValue config value
     * @param string $sVariableType config type
     *
     * @return null
     */
    protected function _saveToConfig( $sVariableName, $sVariableValue, $sVariableType = 'aarr' )
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam( $sVariableName, $sVariableValue );
        $oConfig->saveShopConfVar( $sVariableType, $sVariableName, $sVariableValue );
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

    /**
     * Cleans PHP APC cache
     */
    protected function _clearApcCache()
    {
        if ( extension_loaded( 'apc' ) && ini_get( 'apc.enabled' ) ) {
            apc_clear_cache();
        }
    }

    /**
     * Removes module from disabled module list
     */
    protected function _removeFromDisabledList()
    {
        $aDisabledModules = $this->getDisabledModules();
        $sModuleId = $this->getId();

        if ( isset( $aDisabledModules ) && is_array( $aDisabledModules ) ) {
            $aDisabledModules = array_diff( $aDisabledModules, array($sModuleId) );
            $this->_saveToConfig( 'aDisabledModules', $aDisabledModules, 'arr' );
        }
    }

    /**
     * Add extension to module
     */
    protected function _addExtensions()
    {
        $aModules = $this->_removeNotUsedExtensions( $this->getModulesWithExtendedClass() );

        if ( $this->hasExtendClass() ) {
            $aAddModules  = $this->getExtensions();
            $aModules = $this->mergeModuleArrays( $aModules, $aAddModules );
        }

            $aModules = $this->buildModuleChains( $aModules );

        $this->_saveToConfig( 'aModules', $aModules );
        }

    /**
     * Add module to disable list
     */
    protected function _addToDisabledList( $sModuleId )
    {
        $aDisabledModules = $this->getDisabledModules();

        if ( !is_array( $aDisabledModules ) ) {
            $aDisabledModules = array();
        }
        $aModules = array_merge( $aDisabledModules, array( $sModuleId ) );
        $aModules = array_unique( $aModules );

        $this->_saveToConfig( 'aDisabledModules', $aModules, 'arr' );
    }

    /**
     * Removes garbage ( module not used extensions ) from all installed extensions list
     *
     * @param $aInstalledExtensions
     *
     * @return array
     */
    protected function _removeNotUsedExtensions( $aInstalledExtensions )
    {
        $aModuleExtensions = $this->getExtensions();

        $aInstalledModuleExtensions = $this->filterModuleArray( $aInstalledExtensions, $this->getId() );

        if ( count( $aInstalledModuleExtensions ) ) {
            $aGarbage = $this->_getModuleExtensionsGarbage( $aModuleExtensions, $aInstalledModuleExtensions );

            if ( count( $aGarbage ) ) {
                $aInstalledExtensions = $this->_removeGarbage( $aInstalledExtensions, $aGarbage );
            }
        }

        return $aInstalledExtensions;
    }

    /**
     * Returns extension which is no longer in metadata - garbage
     *
     * @param $aModuleMetaDataExtensions - extensions defined in metadata.
     * @param $aModuleInstalledExtensions - extensions which are installed
     *
     * @return array
     */
    protected function _getModuleExtensionsGarbage( $aModuleMetaDataExtensions, $aModuleInstalledExtensions )
    {
        $aGarbage = $aModuleInstalledExtensions;

        foreach ( $aModuleMetaDataExtensions as $sClassName => $sClassPath ) {
            if ( isset( $aGarbage[$sClassName] ) ) {
                unset( $aGarbage[$sClassName][ array_search( $sClassPath, $aGarbage[$sClassName] )] );
                if ( count( $aGarbage[$sClassName] ) == 0 ) {
                    unset( $aGarbage[$sClassName] );
                }
            }
        }

        return $aGarbage;
    }

    /**
     * Removes garbage - not exiting module extensions, returns clean array of installed extensions
     *
     * @param $aInstalledExtensions - all installed extensions ( from all modules )
     * @param $aGarbage - extension which are not used and should be removed
     *
     * @return array
     */
    protected function _removeGarbage( $aInstalledExtensions, $aGarbage )
    {
        foreach ( $aGarbage as $sClassName => $aClassPaths ) {
            foreach ( $aClassPaths as $sClassPath ) {
                if ( isset( $aInstalledExtensions[$sClassName] ) ) {
                    unset( $aInstalledExtensions[$sClassName][ array_search( $sClassPath, $aInstalledExtensions[$sClassName] )] );
                    if ( count( $aInstalledExtensions[$sClassName] ) == 0 ) {
                        unset( $aInstalledExtensions[$sClassName] );
                    }
                }
            }
        }

        return $aInstalledExtensions;
    }

    /**
     * Counts activated module extensions.
     *
     * @param $aModuleExtensions
     * @param $aInstalledExtensions
     * @return int
     */
    protected function _countActivatedExtensions( $aModuleExtensions, $aInstalledExtensions )
    {
        $iActive = 0;
        foreach ( $aModuleExtensions as $sClass => $mExtension ) {
            if ( is_array( $mExtension ) ) {
                foreach ( $mExtension as $sExtension ) {
                    if ( ( isset( $aInstalledExtensions[$sClass] ) && in_array( $sExtension, $aInstalledExtensions[$sClass] ) ) ) {
                        $iActive++;
                    }
                }
            } elseif ( ( isset( $aInstalledExtensions[$sClass] ) && in_array( $mExtension, $aInstalledExtensions[$sClass] ) ) ) {
                $iActive++;
            }
        }

        return $iActive;
    }

    /**
     * Counts module extensions.
     *
     * @param $aModuleExtensions
     * @return int
     */
    protected function _countExtensions( $aModuleExtensions )
    {
        $iCount = 0;
        foreach ( $aModuleExtensions as $mExtensions ) {
            if ( is_array( $mExtensions ) ) {
                $iCount += count( $mExtensions );
            } else {
                $iCount++;
            }
        }

        return $iCount;
    }

    /**
     * Checks if module extensions count is the same as in activated extensions list.
     *
     * @return bool
     */
    protected function _isExtensionsActive()
    {
        $aModuleExtensions = $this->getExtensions();
        $aInstalledExtensions = $this->getModulesWithExtendedClass();
        $iModuleExtensionsCount = $this->_countExtensions( $aModuleExtensions );
        $iActivatedModuleExtensionsCount = $this->_countActivatedExtensions( $aModuleExtensions, $aInstalledExtensions );
        $blActive = $iModuleExtensionsCount > 0 && $iActivatedModuleExtensionsCount == $iModuleExtensionsCount;

        return $blActive;
    }

    /**
     * Checks if module is in disabled list.
     *
     * @param $sId
     * @return bool
     */
    protected function _isInDisabledList( $sId )
    {
        $blInDisabledList = false;

        $aDisabledModules = $this->getDisabledModules();
        if ( is_array( $aDisabledModules ) && in_array( $sId, $aDisabledModules ) ) {
            $blInDisabledList = true;
        }

        return $blInDisabledList;
    }
}