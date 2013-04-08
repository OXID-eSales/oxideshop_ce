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
 * @version   SVN: $Id$
 */

/**
 * Class handling shop modules.
 *
 */
class oxModuleList extends oxSuperCfg
{
    /**
     * Modules info array
     *
     * @var array
     */
    protected $_aModule = array();

    /**
     * List of files that should be skipped while scaning modules dir
     *
     * @var array
     */
    protected $_aSkipFiles = array( "functions.php", "vendormetadata.php" );


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
     * Get parsed active modules info
     *
     * @return array
     */
    public function getActiveModuleInfo()
    {
        $aModules       = $this->getAllModules();
        $aModulePaths   = $this->getModulePaths();
        if ( !is_array($aModulePaths) || count($aModulePaths) < 1 ) {
           // $aModulePaths = array();
            if ( is_array($aModules) && count($aModules) > 0 ) {
                foreach ($aModules as $aModuleClasses) {
                    foreach ($aModuleClasses as $sModule) {
                        $sModuleId = substr( $sModule, 0, strpos( $sModule, "/" ) );
                        $aModulePaths[$sModuleId] = $sModuleId;
                    }
                }
            }
        }

        $aDisabledModules = $this->getDisabledModules();
        if ( is_array($aDisabledModules) && count($aDisabledModules) > 0  && count($aModulePaths) > 0 ) {
            $aModulePaths = array_diff_key($aModulePaths, array_flip($aDisabledModules));
        }
        return $aModulePaths;
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
     * Get all modules files paths
     *
     * @return array
     */
    public function getModuleFiles()
    {
        return $this->getConfig()->getConfigParam('aModuleFiles');
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
        $aModules         = $this->getAllModules();
        $aModulePaths     = $this->getModulePaths();

        $aDisabledModuleClasses = array();
        if (isset($aDisabledModules) && is_array($aDisabledModules)) {
            //get all disabled module paths
            foreach ($aDisabledModules as $sId) {
                $sPath = $aModulePaths[$sId];
                if (!isset($sPath)) {
                    $sPath = $sId;
                }
                foreach ( $aModules as $sClass => $aModuleClasses ) {
                    foreach ( $aModuleClasses as $sModuleClass ) {
                        if (strpos($sModuleClass, $sPath."/") === 0 ) {
                            $aDisabledModuleClasses[] = $sModuleClass;
                        }
                    }
                }
            }
        }

        return $aDisabledModuleClasses;
    }

    /**
     * Removes extension metadata from eshop
     *
     * @return null
     */
    public function cleanup()
    {
        $aDeletedExt = $this->getDeletedExtensions();

        //collecting deleted extension IDs
        $aDeletedExtIds = $this->getDeletedExtensionIds($aDeletedExt);

        // removing from aModules config varviable
        $this->_removeFromModulesArray( $aDeletedExt );

        // removing from aDisabledModules config varviable
        $this->_removeFromDisabledModulesArray( $aDeletedExtIds );

        // removing from aLegacyModules array
        $this->_removeFromLegacyModulesArray( $aDeletedExtIds );

        // removing from aModulePaths array
        $this->_removeFromModulesPathsArray( $aDeletedExtIds );

        // removing from aModuleFiles array
        $this->_removeFromModulesFilesArray( $aDeletedExtIds );

        // removing from aModuleTemplates array
        $this->_removeFromModulesTemplatesArray( $aDeletedExtIds );

        //removing from config tables and templates blocks table
        $this->_removeFromDatabase( $aDeletedExtIds );
    }

    /**
     * Returns deleted extension Ids
     *
     * @param array $aDeletedExt deleted extensions
     *
     * @return array
     */
    public function getDeletedExtensionIds($aDeletedExt)
    {
        $aDeletedExtIds = array();
        if ( !empty($aDeletedExt) ) {
            $oModule = oxNew("oxModule");
            foreach ( $aDeletedExt as $sOxClass => $aDeletedModules ) {
                foreach ( $aDeletedModules as $sModulePath ) {
                    $aDeletedExtIds[] = $oModule->getIdByPath($sModulePath);
                }
            }
        }

        if ( !empty( $aDeletedExtIds ) ) {
            $aDeletedExtIds = array_unique( $aDeletedExtIds );
        }

        return $aDeletedExtIds;
    }

    /**
     * Checks moduels list - if there is extensions that are registered, but
     * extension directory is missing
     *
     * @return array
     */
    public function getDeletedExtensions()
    {
        $aModules = $this->getAllModules();

        foreach ( $aModules as $sOxClass => $aModulesList ) {
            foreach ( $aModulesList as $sModulePath ) {
                $sExtPath = $this->getConfig()->getModulesDir() . $sModulePath.".php";
                if ( !file_exists( $sExtPath ) ) {
                    $aDeletedExt[$sOxClass][] = $sModulePath;
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
     * Removes extension from modules array
     *
     * @param array $aDeletedExt Deleated extendion array
     *
     * @return null
     */
    protected function _removeFromModulesArray( $aDeletedExt )
    {
        $aExt = $this->getAllModules();
        $aUpdatedExt = $this->diffModuleArrays( $aExt, $aDeletedExt );
        $aUpdatedExt = $this->buildModuleChains( $aUpdatedExt );

        $this->getConfig()->saveShopConfVar( 'aarr', 'aModules', $aUpdatedExt );
    }

    /**
     * Removes extension from disabled modules array
     *
     * @param array $aDeletedExtIds Id's of deleated extendion array
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
     * Removes extension from legacy modules array
     *
     * @param array $aDeletedExtIds deleted extensions ID's
     *
     * @return null
     */
    protected function _removeFromLegacyModulesArray( $aDeletedExtIds )
    {
        $aLegacyExt = $this->getLegacyModules();

        foreach ( $aDeletedExtIds as $sDeletedExtId ) {
            if ( isset($aLegacyExt[$sDeletedExtId]) ) {
                unset( $aLegacyExt[$sDeletedExtId] );
            }
        }

        $this->getConfig()->saveShopConfVar( 'aarr', 'aLegacyModules', $aLegacyExt );
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
     * Removes extension from legacy modules templates array
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
     * Removes extension from database - oxconfig, oxconfigdisplay and oxtplblocks tables
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
        $sDelExtIds = array();

        foreach ( $aDeletedExtIds as $sDeletedExtId ) {
            $aConfigIds[] = "'module:" . $sDeletedExtId . "'";
            $sDelExtIds[] = "'" . $sDeletedExtId . "'";
        }

        $sConfigIds = implode( ", ", $aConfigIds );
        $sDelExtIds = implode( ", ", $sDelExtIds );

        $aSql[] = "DELETE FROM oxconfig where oxmodule IN ($sConfigIds)";
        $aSql[] = "DELETE FROM oxconfigdisplay where oxcfgmodule IN ($sConfigIds)";
        $aSql[] = "DELETE FROM oxtplblocks where oxmodule IN ($sDelExtIds)";

        $oDb = oxDb::getDb();
        foreach ( $aSql as $sQuery ) {
            $oDb->execute( $sQuery );
        }
    }

    /**
     * Scans modules dir and returns colected modules list. Recursively loads also modules that
     * are in vendor directory.
     *
     * @param string $sModulesDir Main module dir path
     * @param string $sVendorDir  Vendor directory name
     *
     * @return array
     */
    public function getModulesFromDir( $sModulesDir, $sVendorDir = null )
    {
        $sModulesDir  = oxUtilsFile::getInstance()->normalizeDir( $sModulesDir );

        foreach ( glob( $sModulesDir."*" ) as $sModuleDirPath ) {

            $sModuleDirPath .= ( is_dir( $sModuleDirPath ) ) ? "/" : "";
            $sModuleDirName  = basename( $sModuleDirPath );

            // skipping some file
            if ( in_array( $sModuleDirName, $this->_aSkipFiles ) || (!is_dir( $sModuleDirPath ) && substr($sModuleDirName, -4) != ".php")) {
                continue;
            }

            if ( $this->_isVendorDir( $sModuleDirPath ) ) {
                // scaning modules vendor directory
                $this->getModulesFromDir( $sModuleDirPath, basename( $sModuleDirPath ) );
            } else {
                // loading module info
                $oModule = oxNew( "oxModule" );
                $sModuleDirName = ( !empty($sVendorDir) ) ? $sVendorDir."/".$sModuleDirName : $sModuleDirName;
                $oModule->loadByDir( $sModuleDirName );
                $sModuleId = $oModule->getId();
                $this->_aModules[$sModuleId] = $oModule;

                $aModulePaths = $this->getModulePaths();

                if ( !is_array($aModulePaths) || !array_key_exists( $sModuleId, $aModulePaths ) ) {
                    // saving module path info
                    $this->_saveModulePath( $sModuleId, $sModuleDirName );

                    //checking if this is new module and if it extends any eshop class
                    if ( !$this->_extendsClasses( $sModuleDirName ) ) {
                        // if not - marking it as disabled by default
                        $oModule->deactivate();
                    }
                }
            }
        }
        // sorting by name
        if ( $this->_aModules !== null ) {
            uasort($this->_aModules, array($this, '_sortModules'));
        }

        return $this->_aModules;
    }

    /**
     * Callback function for sorting module objects by name.
     *
     * @param object $oModule1 module object
     * @param object $oModule2 module object
     *
     * @return bool
     */
    protected function _sortModules( $oModule1, $oModule2 )
    {
        return strcasecmp($oModule1->getTitle(), $oModule2->getTitle());
    }

    /**
     * Checks if directory is vedor directory.
     *
     * @param string $sModuleDir dir path
     *
     * @return bool
     */
    protected function _isVendorDir( $sModuleDir )
    {
        if ( is_dir( $sModuleDir ) && file_exists( $sModuleDir . "vendormetadata.php" ) ) {
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
    protected function _extendsClasses ( $sModuleDir )
    {
        $aModules = $this->getConfig()->getConfigParam( "aModules" );
        if (is_array($aModules)) {
            $sModules = implode( "&", $aModules );

            if ( preg_match("@(^|&+)".$sModuleDir."\b@", $sModules ) ) {
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
     *
     * @return null
     */
    protected function _saveModulePath( $sModuleId, $sModulePath )
    {
        $aModulePaths = $this->getModulePaths();

        $aModulePaths[$sModuleId] = $sModulePath;
        $this->getConfig()->saveShopConfVar( 'aarr', 'aModulePaths', $aModulePaths );
    }

}