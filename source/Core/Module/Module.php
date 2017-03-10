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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use oxDb;
use oxRegistry;

/**
 * Module class.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class Module extends \OxidEsales\Eshop\Core\Base
{

    /**
     * Metadata version as defined in metadata.php
     */
    protected $metaDataVersion;

    /**
     * @return mixed
     */
    public function getMetaDataVersion()
    {
        return $this->metaDataVersion;
    }

    /**
     * @param mixed $metaDataVersion
     */
    public function setMetaDataVersion($metaDataVersion)
    {
        $this->metaDataVersion = $metaDataVersion;
    }

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
     * Get the modules metadata array
     *
     * @return  array Module meta data array
     */
    public function getModuleData()
    {
        return $this->_aModule;
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
            $this->includeModuleMetaData($sMetadataPath);
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
        $rawExtensions = isset($this->_aModule['extend']) ? $this->_aModule['extend'] : array();
        return $this->getVirtualShopClassExtensionsForBc($rawExtensions);
    }

    /**
     * Returns associative array of module controller ids and corresponding classes.
     *
     * @return array
     */
    public function getControllers()
    {
        if (isset($this->_aModule['controllers']) && ! is_array($this->_aModule['controllers'])) {
            throw new \InvalidArgumentException('Value for metadata key "controllers" must be an array');
        }

        return isset($this->_aModule['controllers']) ? array_change_key_case($this->_aModule['controllers']) : array();
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
     * @param string $module extension full path
     *
     * @return string
     */
    public function getIdByPath($module)
    {
        $moduleId = null;
        $moduleFile = $module;
        $moduleId = $this->getIdFromExtension($module);
        if (!$moduleId) {
            $modulePaths = $this->getConfig()->getConfigParam('aModulePaths');

            if (is_array($modulePaths)) {
                foreach ($modulePaths as $id => $path) {
                    if (strpos($moduleFile, $path . "/") === 0) {
                        $moduleId = $id;
                    }
                }
            }
        }
        if (!$moduleId) {
            $moduleId = substr($moduleFile, 0, strpos($moduleFile, "/"));
        }
        if (!$moduleId) {
            $moduleId = $moduleFile;
        }

        return $moduleId;
    }

    /**
     * Get the module id of given extended class name or namespace.
     *
     * @param string $className
     *
     * @return string
     */
    public function getIdFromExtension($className)
    {
        $moduleId = '';
        $extensions = (array) $this->getConfig()->getConfigParam('aModuleExtensions');
        foreach ($extensions as $id => $moduleClasses) {
            if (in_array($className, $moduleClasses)) {
                $moduleId = $id;
            }
        }

        return $moduleId;
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
        $sModulePath = (isset($aModulePaths[$sModuleId])) ? $aModulePaths[$sModuleId] : '';

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
     * @todo extract oxtplblocks query to ModuleTemplateBlockRepository
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

        return oxDb::getDb()->getCol("SELECT oxtemplate FROM oxtplblocks WHERE oxmodule = '$sModuleId' AND oxshopid = '$sShopId'");
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

        return $iModuleExtensionsCount > 0 && $iActivatedModuleExtensionsCount == $iModuleExtensionsCount;
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
        return in_array($sId, (array) $this->getConfig()->getConfigParam('aDisabledModules'));
    }

    /**
     * Include data from metadata.php
     *
     * @param string $sMetadataPath
     */
    protected function includeModuleMetaData($sMetadataPath)
    {
        include $sMetadataPath;
        /**
         * Metadata should include a variable called $aModule, if this variable is not set,
         * an empty array is assigned to self::aModule
         */
        if (!isset($aModule)) {
            $aModule = array();
        }
        $this->setModuleData($aModule);

        if (isset($sMetadataVersion)) {
            $this->setMetaDataVersion($sMetadataVersion);
        }
    }

    /**
     * @deprecated since v6.0.0 (2017-03-14); Needed to ensure backwards compatibility.
     *
     * Translate module metadata information about patched shop classes
     * into virtual namespace. There might still be BC class names used in module metadata.php.
     *
     * @param array $rawExtensions Extension information from module metadata.php.
     *
     * @return array
     */
    protected function getVirtualShopClassExtensionsForBc($rawExtensions)
    {
        $extensions = [];

        foreach ($rawExtensions as $classToBePatched => $moduleClass) {
            if (!\OxidEsales\Eshop\Core\UtilsObject::isNamespacedClass($classToBePatched)) {
                $bcMap = oxRegistry::getBackwardsCompatibilityClassMap();
                $classToBePatched = $bcMap[strtolower($classToBePatched)] ?: $classToBePatched;
            }
            $extensions[$classToBePatched] = $moduleClass;
        }
        return $extensions;
    }
}
