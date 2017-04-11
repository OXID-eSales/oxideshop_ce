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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 *
 * @link      http://www.oxid-esales.com
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 * @version   SVN: $Id$
 */

/**
 * Class oxTestModuleLoader
 */
class oxTestModuleLoader
{
    /** @var array  */
    protected static $_aModuleData = array('chains' => array(), 'paths' => array());

    /** @var bool  */
    protected static $_blOriginal = false;

    /**
     * Sets the original chain loading command
     *
     * @param boolean $blOriginal
     */
    public function useOriginalChain($blOriginal)
    {
        self::$_blOriginal = $blOriginal;
    }

    /**
     * Tries to initiate the module classes and includes required files from metadata
     *
     * @param array $aModulesPath Array of modules to load.
     */
    public function loadModules($aModulesPath)
    {
        $aErrors = array();
        $sBaseModuleDir = oxRegistry::getConfig()->getConfigParam("sShopDir") . "/modules/";
        $aModulesPath = is_array($aModulesPath) ? $aModulesPath : array($aModulesPath);

        foreach ($aModulesPath as $sPath) {
            if (file_exists($sBaseModuleDir . $sPath . "/metadata.php")) {
                self::$_aModuleData['paths'][] = $sPath;
                self::_initMetadata($sBaseModuleDir . $sPath . "/metadata.php");
            } else {
                $aErrors[] = "Unable to find metadata file in directory: $sPath" . PHP_EOL;
            }
        }

        if ($aErrors) {
            die(implode("\n\n", $aErrors));
        }
    }

    /**
     * Loads the module from metadata file
     * If no metadata found and the module chain is empty, then does nothing.
     *
     * On first load the data is saved and on consecutive calls the saved data is used
     */
    public function setModuleInformation()
    {
        if (count(self::$_aModuleData['chains'])) {
            $oUtilsObject = oxRegistry::get("oxUtilsObject");
            $oConfig = oxRegistry::getConfig();

            $oUtilsObject->setModuleVar("aModules", self::$_aModuleData['chains']);
            $oConfig->setConfigParam("aModules", self::$_aModuleData['chains']);
            $oUtilsObject->setModuleVar("aDisabledModules", array());
            $oConfig->setConfigParam("aDisabledModules", array());
            $oUtilsObject->setModuleVar("aModulePaths", self::$_aModuleData['paths']);
            $oConfig->setConfigParam("aModulePaths", self::$_aModuleData['paths']);
        }
    }

    /**
     * Returns modules path.
     *
     * @return string
     */
    protected function _getModulesPath()
    {
        return oxRegistry::getConfig()->getConfigParam("sShopDir") . "/modules/";
    }

    /**
     * Loads the module files and extensions from the given metadata file
     *
     * @param string $sPath path to the metadata file
     */
    private function _initMetadata($sPath)
    {
        include $sPath;

        // including all filles from ["files"]
        if (isset($aModule["files"]) && count($aModule["files"])) {
            $this->_includeModuleFiles($aModule["files"]);
        }

        // adding and extending the module files
        if (isset($aModule["extend"]) && count($aModule["extend"])) {
            $this->_appendToChain($aModule["extend"]);
        }

        // running onActivate method.
        if (isset($aModule["events"]) && isset($aModule["events"]["onActivate"])) {
            if (is_callable($aModule["events"]["onActivate"])) {
                call_user_func($aModule["events"]["onActivate"]);
            }
        }
    }

    /**
     * Include module files.
     *
     * @param array $aFiles
     */
    private function _includeModuleFiles($aFiles)
    {
        foreach ($aFiles as $sFilePath) {
            $sClassName = basename($sFilePath);
            $sClassName = substr($sClassName, 0, strlen($sClassName) - 4);

            if (!class_exists($sClassName, false)) {
                require oxRegistry::getConfig()->getConfigParam("sShopDir") . "/modules/" . $sFilePath;
            }
        }
    }

    /**
     * Appends extended files to module chain.
     * Adds to "original" chain if needed.
     * Adding the "extend" chain to the main chain.
     *
     * @param array $aExtend
     */
    private function _appendToChain($aExtend)
    {
        if (self::$_blOriginal && !count(self::$_aModuleData['chains'])) {
            self::$_aModuleData['chains'] = (array)modConfig::getInstance()->getConfigParam("aModules");
        }

        foreach ($aExtend as $sParent => $sExtends) {
            if (isset(self::$_aModuleData['chains'][$sParent])) {
                $sExtends = trim(self::$_aModuleData['chains'][$sParent], "& ") . "&"
                    . trim($sExtends, "& ");
            }
            self::$_aModuleData['chains'][$sParent] = $sExtends;
        }
    }
}
