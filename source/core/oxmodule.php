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
 * Module class.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
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
    protected $_blMetadata = false;

    /**
     * Defines if module is registered in metadata or legacy storage
     *
     * @var bool
     */
    protected $_blRegistered = false;

    /**
     * Set passed module data
     *
     * @param array $aModule module data
     */
    public function setModuleData($aModule)
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
    public function load($sModuleId)
    {
        $sModulePath = $this->getModuleFullPath($sModuleId);
        $sMetadataPath = $sModulePath . "/metadata.php";

        if ($sModulePath && file_exists($sMetadataPath) && is_readable($sMetadataPath)) {
            $aModule = array();
            include $sMetadataPath;
            $this->_aModule = $aModule;
            $this->_blRegistered = true;
            $this->_blMetadata = true;
            $this->_aModule['active'] = $this->isActive();

            return true;
        }

        return false;
    }

    /**
     * Load module by dir name
     *
     * @param string $sModuleDir Module dir name
     *
     * @return bool
     */
    public function loadByDir($sModuleDir)
    {
        $sModuleId = null;
        $aModulePaths = $this->getModulePaths();

        if (is_array($aModulePaths)) {
            $sModuleId = array_search($sModuleDir, $aModulePaths);
        }

        // if no module id defined, using module dir as id
        if (!$sModuleId) {
            $sModuleId = $sModuleDir;
        }

        return $this->load($sModuleId);
    }

    /**
     * Get module description
     *
     * @return string
     */
    public function getDescription()
    {
        $iLang = oxRegistry::getLang()->getTplLanguage();

        return $this->getInfo("description", $iLang);
    }

    /**
     * Get module title
     *
     * @return string
     */
    public function getTitle()
    {
        $iLang = oxRegistry::getLang()->getTplLanguage();

        return $this->getInfo("title", $iLang);
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
     * Returns array of module extensions.
     *
     * @return array
     */
    public function getExtensions()
    {
        return isset($this->_aModule['extend']) ? $this->_aModule['extend'] : array();
    }

    /**
     * Returns array of module PHP files.
     *
     * @return array
     */
    public function getFiles()
    {
        return isset($this->_aModule['files']) ? $this->_aModule['files'] : array();
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
        $myConfig = $this->getConfig();
        $aModulePaths = $myConfig->getConfigParam('aModulePaths');
        $sModuleId = null;
        if (is_array($aModulePaths)) {
            foreach ($aModulePaths as $sId => $sPath) {
                if (strpos($sModule, $sPath . "/") === 0) {
                    $sModuleId = $sId;
                }
            }
        }
        if (!$sModuleId) {
            $sModuleId = substr($sModule, 0, strpos($sModule, "/"));
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
    public function getInfo($sName, $iLang = null)
    {
        if (isset($this->_aModule[$sName])) {

            if ($iLang !== null && is_array($this->_aModule[$sName])) {
                $sValue = null;

                $sLang = oxRegistry::getLang()->getLanguageAbbr($iLang);

                if (!empty($this->_aModule[$sName])) {
                    if (!empty($this->_aModule[$sName][$sLang])) {
                        $sValue = $this->_aModule[$sName][$sLang];
                    } elseif (!empty($this->_aModule['lang'])) {
                        // trying to get value according default language
                        $sValue = $this->_aModule[$sName][$this->_aModule['lang']];
                    } else {
                        // returning first array value
                        $sValue = reset($this->_aModule[$sName]);
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
        if (!is_null($sId)) {
            $blActive = !$this->_isInDisabledList($sId);
            if ($blActive && $this->hasExtendClass()) {
                $blActive = $this->_isExtensionsActive();
            }
        }

        return $blActive;
    }

    /**
     * Checks if has extend class.
     *
     * @return bool
     */
    public function hasExtendClass()
    {
        $aExtensions = $this->getExtensions();

        return isset($aExtensions)
               && is_array($aExtensions)
               && !empty($aExtensions);
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
     * Get full path to module metadata file.
     *
     * @return string
     */
    public function getMetadataPath()
    {
        $sModulePath = $this->getModuleFullPath();
        if (substr($sModulePath, -1) != DIRECTORY_SEPARATOR) {
            $sModulePath .= DIRECTORY_SEPARATOR;
        }

        return $sModulePath . 'metadata.php';
    }

    /**
     * Get module dir
     *
     * @param string $sModuleId Module ID
     *
     * @return string
     */
    public function getModulePath($sModuleId = null)
    {
        if (!$sModuleId) {
            $sModuleId = $this->getId();
        }

        $aModulePaths = $this->getModulePaths();
        $sModulePath = $aModulePaths[$sModuleId];

        // if still no module dir, try using module ID as dir name
        if (!$sModulePath && is_dir($this->getConfig()->getModulesDir() . $sModuleId)) {
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
    public function getModuleFullPath($sModuleId = null)
    {
        if (!$sModuleId) {
            $sModuleId = $this->getId();
        }

        if ($sModuleDir = $this->getModulePath($sModuleId)) {
            return $this->getConfig()->getModulesDir() . $sModuleDir;
        }

        return false;
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
     * Return templates affected by template blocks for given module id.
     *
     * @param string $sModuleId Module id
     *
     * @return array
     */
    public function getTemplates($sModuleId = null)
    {
        if (is_null($sModuleId)) {
            $sModuleId = $this->getId();
        }

        if (!$sModuleId) {
            return array();
        }

        $sShopId = $this->getConfig()->getShopId();

        $aTemplates = oxDb::getDb()->getCol("SELECT oxtemplate FROM oxtplblocks WHERE oxmodule = '$sModuleId' AND oxshopid = '$sShopId'");

        return $aTemplates;
    }

    /**
     * Counts activated module extensions.
     *
     * @param array $aModuleExtensions    Module extensions
     * @param array $aInstalledExtensions Installed extensions
     *
     * @return int
     */
    protected function _countActivatedExtensions($aModuleExtensions, $aInstalledExtensions)
    {
        $iActive = 0;
        foreach ($aModuleExtensions as $sClass => $mExtension) {
            if (is_array($mExtension)) {
                foreach ($mExtension as $sExtension) {
                    if ((isset($aInstalledExtensions[$sClass]) && in_array($sExtension, $aInstalledExtensions[$sClass]))) {
                        $iActive++;
                    }
                }
            } elseif ((isset($aInstalledExtensions[$sClass]) && in_array($mExtension, $aInstalledExtensions[$sClass]))) {
                $iActive++;
            }
        }

        return $iActive;
    }

    /**
     * Counts module extensions.
     *
     * @param array $aModuleExtensions Module extensions
     *
     * @return int
     */
    protected function _countExtensions($aModuleExtensions)
    {
        $iCount = 0;
        foreach ($aModuleExtensions as $mExtensions) {
            if (is_array($mExtensions)) {
                $iCount += count($mExtensions);
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

        $aInstalledExtensions = $this->getConfig()->getModulesWithExtendedClass();
        $iModuleExtensionsCount = $this->_countExtensions($aModuleExtensions);
        $iActivatedModuleExtensionsCount = $this->_countActivatedExtensions($aModuleExtensions, $aInstalledExtensions);
        $blActive = $iModuleExtensionsCount > 0 && $iActivatedModuleExtensionsCount == $iModuleExtensionsCount;

        return $blActive;
    }

    /**
     * Checks if module is in disabled list.
     *
     * @param string $sId Module id
     *
     * @return bool
     */
    protected function _isInDisabledList($sId)
    {
        $blInDisabledList = false;

        $aDisabledModules = (array) $this->getConfig()->getConfigParam('aDisabledModules');
        if (in_array($sId, $aDisabledModules)) {
            $blInDisabledList = true;
        }

        return $blInDisabledList;
    }
}
