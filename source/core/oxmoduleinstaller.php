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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Class handling shop module list.
 *
 */
class oxModuleInstaller extends oxSuperCfg
{
    /**
     * @var oxModule
     */
    protected $_oModule;

    /**
     * @param oxModule $oModule
     * @param oxModuleCache $oxModuleCache
     */
    public function __construct( oxModule $oModule, oxModuleCache $oxModuleCache = null )
    {
        $this->setModule( $oModule );
    }

    /**
     * @param oxModule $oModule
     */
    public function setModule( $oModule )
    {
        $this->_oModule = $oModule;
    }

    /**
     * @return oxModule
     */
    public function getModule()
    {
        return $this->_oModule;
    }

    /**
     * Activate extension by merging module class inheritance information with shop module array
     *
     * @return bool
     */
    public function activate()
    {
        $blResult = false;
        $oModule = $this->getModule();

        if ( $oModule->hasMetadata() || $oModule->hasExtendClass() ) {

            $this->_addExtensions();

            $this->_removeFromDisabledList();

            $this->_addTemplateBlocks( $oModule->getInfo("blocks") );

            // Register new module files
            $this->_addModuleFiles($oModule->getInfo("files") );

            // Register new module templates
            $this->_addTemplateFiles( $oModule->getInfo("templates") );

            // Add module settings
            $this->_addModuleSettings( $oModule->getInfo("settings") );

            // Add module version
            $this->_addModuleVersion( $oModule->getInfo("version") );

            // Add module events
            $this->_addModuleEvents( $oModule->getInfo("events") );

            //resets cache
            $this->_getModuleCache()->resetCache();


            $this->_callEvent('onActivate',  $oModule->getId() );

            $blResult = true;
        }

        return $blResult;
    }

    /**
     * Deactivate extension by adding disable module class information to disabled module array
     *
     * @return bool
     */
    public function deactivate()
    {
        $sModuleId = $this->getModule()->getId();
        $this->_addToDisabledList( $sModuleId );

        $this->_callEvent( 'onDeactivate', $sModuleId );

        //resets cache
        $this->_getModuleCache()->resetCache();

        //removing recoverable options
        $this->_deleteBlock( $sModuleId );
        $this->_deleteTemplateFiles( $sModuleId );
        $this->_deleteModuleFiles( $sModuleId );
        $this->_deleteModuleEvents( $sModuleId );
        $this->_deleteModuleVersions( $sModuleId );


        return true;
    }

    /**
     * Removes extension metadata from shop.
     *
     * @return null
     */
    public function remove()
    {
        // removing from aModules config array
        $this->_removeFromModulesArray( $aDeletedExt );

        // removing from aDisabledModules array
        $this->_removeFromDisabledModulesArray( $aDeletedExtIds );

        // removing from aModulePaths array
        $this->_removeFromModulesPathsArray( $aDeletedExtIds );

        // removing from aModuleEvents array
        $this->_removeFromModulesEventsArray( $aDeletedExtIds );

        // removing from aModuleVersions array
        $this->_removeFromModulesVersionsArray( $aDeletedExtIds );

        // removing from aModuleFiles array
        $this->_removeFromModulesFilesArray( $aDeletedExtIds );

        // removing from aModuleTemplates array
        $this->_removeFromModulesTemplatesArray( $aDeletedExtIds );

        //removing from config tables and templates blocks table
        $this->_removeFromDatabase( $aDeletedExtIds );
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
    public function buildModuleChains( $aModuleArray )
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
     * Removes extension from modules array
     *
     * @param array $aDeletedExt Deleted extension array
     *
     * @return null
     */
    protected function _removeFromModulesArray( $aDeletedExt )
    {
        $aExt = $this->getModulesWithExtendedClass();
        $aUpdatedExt = $this->diffModuleArrays( $aExt, $aDeletedExt );
        $aUpdatedExt = $this->buildModuleChains( $aUpdatedExt );

        $this->getConfig()->saveShopConfVar( 'aarr', 'aModules', $aUpdatedExt );
    }

    /**
     * Removes extension from disabled modules array
     *
     * @param array $aDeletedExtIds Deleted extension id's of array
     *
     * @return null
     */
    protected function _removeFromDisabledModulesArray( $aDeletedExtIds )
    {
        $oConfig = $this->getConfig();
        $aDisabledExtensionIds = $this->getDisabledModules();
        $aDisabledExtensionIds = array_diff($aDisabledExtensionIds, $aDeletedExtIds);
        $oConfig->saveShopConfVar( 'arr', 'aDisabledModules', $aDisabledExtensionIds );
    }

    /**
     * Removes extension from modules paths array
     *
     * @param array $aDeletedModule deleted extensions ID's
     *
     * @return null
     */
    protected function _removeFromModulesPathsArray( $aDeletedModule )
    {
        $aModulePaths = $this->getModulePaths();

        foreach ( $aDeletedModule as $sDeletedModuleId ) {
            if ( isset($aModulePaths[$sDeletedModuleId]) ) {
                unset( $aModulePaths[$sDeletedModuleId] );
            }
        }

        $this->getConfig()->saveShopConfVar( 'aarr', 'aModulePaths', $aModulePaths );
    }

    /**
     * Removes extension from modules versions array
     *
     * @param array $aDeletedModule deleted extensions ID's
     *
     * @return null
     */
    protected function _removeFromModulesVersionsArray( $aDeletedModule )
    {
        $aModuleVersions = $this->getModuleVersions();

        foreach ( $aDeletedModule as $sDeletedModuleId ) {
            if ( isset($aModuleVersions[$sDeletedModuleId]) ) {
                unset( $aModuleVersions[$sDeletedModuleId] );
            }
        }

        $this->getConfig()->saveShopConfVar( 'aarr', 'aModuleVersions', $aModuleVersions );
    }

    /**
     * Removes extension from modules events array
     *
     * @param array $aDeletedModule deleted extensions ID's
     *
     * @return null
     */
    protected function _removeFromModulesEventsArray( $aDeletedModule )
    {
        $aModuleEvents = $this->getModuleEvents();

        foreach ( $aDeletedModule as $sDeletedModuleId ) {
            if ( isset($aModuleEvents[$sDeletedModuleId]) ) {
                unset( $aModuleEvents[$sDeletedModuleId] );
            }
        }

        $this->getConfig()->saveShopConfVar( 'aarr', 'aModuleEvents', $aModuleEvents );
    }

    /**
     * Removes extension from modules files array
     *
     * @param array $aDeletedModule deleted extensions ID's
     *
     * @return null
     */
    protected function _removeFromModulesFilesArray( $aDeletedModule )
    {
        $aModuleFiles = $this->getModuleFiles();

        foreach ( $aDeletedModule as $sDeletedModuleId ) {
            if ( isset($aModuleFiles[$sDeletedModuleId]) ) {
                unset( $aModuleFiles[$sDeletedModuleId] );
            }
        }

        $this->getConfig()->saveShopConfVar( 'aarr', 'aModuleFiles', $aModuleFiles );
    }

    /**
     * Removes extension from modules templates array
     *
     * @param array $aDeletedModule deleted extensions ID's
     *
     * @return null
     */
    protected function _removeFromModulesTemplatesArray( $aDeletedModule )
    {
        $aModuleTemplates = $this->getModuleTemplates();

        foreach ( $aDeletedModule as $sDeletedModuleId ) {
            if ( isset($aModuleTemplates[$sDeletedModuleId]) ) {
                unset( $aModuleTemplates[$sDeletedModuleId] );
            }
        }

        $this->getConfig()->saveShopConfVar( 'aarr', 'aModuleTemplates', $aModuleTemplates );
    }

    /**
     * Removes extension from database - oxConfig, oxConfigDisplay and oxTplBlocks tables
     *
     * @param array $aDeletedExtIds deleted extensions ID's
     *
     * @return null
     */
    protected function _removeFromDatabase( $aDeletedExtIds )
    {
        if ( !is_array($aDeletedExtIds) || !count($aDeletedExtIds) ) {
            return;
        }

        $oDb = oxDb::getDb();

        $aConfigIds = $sDelExtIds = array();
        foreach ( $aDeletedExtIds as $sDeletedExtId ) {
            $aConfigIds[] = $oDb->quote('module:'.$sDeletedExtId);
            $sDelExtIds[] = $oDb->quote($sDeletedExtId);
        }

        $sConfigIds = implode( ', ', $aConfigIds );
        $sDelExtIds = implode( ', ', $sDelExtIds );

        $aSql[] = "DELETE FROM oxconfig where oxmodule IN ($sConfigIds)";
        $aSql[] = "DELETE FROM oxconfigdisplay where oxcfgmodule IN ($sConfigIds)";
        $aSql[] = "DELETE FROM oxtplblocks where oxmodule IN ($sDelExtIds)";

        foreach ( $aSql as $sQuery ) {
            $oDb->execute( $sQuery );
        }
    }

    /**
     * Add module to disable list
     */
    protected function _addToDisabledList( $sModuleId )
    {
        $aDisabledModules = $this->getConfig()->getConfigParam('aDisabledModules');

        if ( !is_array( $aDisabledModules ) ) {
            $aDisabledModules = array();
        }
        $aModules = array_merge( $aDisabledModules, array( $sModuleId ) );
        $aModules = array_unique( $aModules );

        $this->_saveToConfig( 'aDisabledModules', $aModules, 'arr' );
    }

    /**
     * Removes extension from modules array
     *
     * @return null
     */
    protected function _deleteModule()
    {
        $sModuleId = $this->getModule()->getId();
        $aExt = $this->getConfig()->getModulesWithExtendedClass();

        $aUpdatedExt = $this->diffModuleArrays( $aExt, $sModuleId );
        $aUpdatedExt = $this->buildModuleChains( $aUpdatedExt );

        $this->getConfig()->saveShopConfVar( 'aarr', 'aModules', $aUpdatedExt );
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
     * Add module template files to config for smarty.
     *
     * @param string $sModuleId        Module id
     *
     * @return null
     */
    protected function _deleteTemplateFiles( $sModuleId )
    {
        $aTemplates = $this->getConfig()->getConfigParam('aModuleTemplates');
        unset( $aTemplates[$sModuleId] );

        $this->_saveToConfig( 'aModuleTemplates', $aTemplates );
    }

    /**
     * Add module files
     *
     * @param string $sModuleId Module id
     *
     * @return null
     */
    protected function _deleteModuleFiles( $sModuleId )
    {
        $aFiles = $this->getConfig()->getConfigParam('aModuleFiles');
        unset( $aFiles[$sModuleId] );

        $this->_saveToConfig( 'aModuleFiles', $aFiles );
    }

    /**
     * Removes module events
     *
     * @param string $sModuleId Module id
     *
     * @return null
     */
    protected function _deleteModuleEvents( $sModuleId )
    {
        $aEvents = $this->getConfig()->getConfigParam('aModuleEvents');
        unset( $aEvents[$sModuleId] );

        $this->_saveToConfig( 'aModuleEvents', $aEvents );
    }

    /**
     * Removes module versions
     *
     * @param string $sModuleId Module id
     *
     * @return null
     */
    protected function _deleteModuleVersions( $sModuleId )
    {
        $aVersions = $this->getConfig()->getConfigParam('aModuleVersions');
        unset( $aVersions[$sModuleId] );

        $this->_saveToConfig( 'aModuleVersions', $aVersions );
    }

    /**
     * Add extension to module
     */
    protected function _addExtensions()
    {
        $aModules = $this->_removeNotUsedExtensions( $this->getModulesWithExtendedClass() );

        if ( $this->getModule()->hasExtendClass() ) {
            $aAddModules  = $this->getModule()->getExtensions();
            $aModules = $this->getModule()->mergeModuleArrays( $aModules, $aAddModules );
        }

        $aModules = $this->buildModuleChains( $aModules );

        $this->_saveToConfig( 'aModules', $aModules );
    }

    /**
     * Removes module from disabled module list
     */
    protected function _removeFromDisabledList()
    {
        $aDisabledModules = $this->getConfig()->getConfigParam('aDisabledModules');
        $sModuleId = $this->getModule()->getId();

        if ( isset( $aDisabledModules ) && is_array( $aDisabledModules ) ) {
            $aDisabledModules = array_diff( $aDisabledModules, array($sModuleId) );
            $this->_saveToConfig( 'aDisabledModules', $aDisabledModules, 'arr' );
        }
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
            $sModuleId = $this->getModule()->getId();
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
            $sModuleId = $this->getModule()->getId();
        }

        $aFiles  = $this->getConfig()->getConfigParam('aModuleFiles');
        if ( is_array($aModuleFiles) ) {
            $aFiles[$sModuleId] = array_change_key_case($aModuleFiles, CASE_LOWER);
        }

        $this->_saveToConfig( 'aModuleFiles', $aFiles );
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
            $sModuleId = $this->getModule()->getId();
        }

        $aTemplates = $this->getConfig()->getConfigParam('aModuleTemplates');
        if ( is_array($aModuleTemplates) ) {
            $aTemplates[$sModuleId] = $aModuleTemplates;
        }

        $this->_saveToConfig( 'aModuleTemplates', $aTemplates );
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
            $sModuleId = $this->getModule()->getId();
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
            $sModuleId = $this->getModule()->getId();
        }

        $aEvents  = $this->getConfig()->getConfigParam('aModuleEvents');
        if ( is_array($aEvents) ) {
            $aEvents[$sModuleId] = $aModuleEvents;
        }

        $this->_saveToConfig( 'aModuleEvents', $aEvents );
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
            $sModuleId = $this->getModule()->getId();
        }

        $aVersions  = $this->getConfig()->getConfigParam('aModuleVersions');
        if ( is_array($aVersions) ) {
            $aVersions[$sModuleId] = $sModuleVersion;
        }

        $this->_saveToConfig( 'aModuleVersions', $aVersions );
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
        $aModuleEvents = $this->getConfig()->getConfigParam('aModuleEvents');

        if ( isset( $aModuleEvents[$sModuleId], $aModuleEvents[$sModuleId][$sEvent] ) ) {
            $mEvent = $aModuleEvents[$sModuleId][$sEvent];

            if ( is_callable( $mEvent ) ) {
                call_user_func($mEvent);
            }
        }
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
        $aModuleExtensions = $this->getModule()->getExtensions();

        $aInstalledModuleExtensions = $this->filterModuleArray( $aInstalledExtensions, $this->getModule()->getId() );

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
     * @return oxModuleCache
     */
    protected function _getModuleCache()
    {
        $oModuleCache = oxNew( 'oxModuleCache' );

        return $oModuleCache;
    }
}