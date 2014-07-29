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

class oxModuleFilesValidator implements oxIModuleValidator
{
    /**
     * Module which files are validating.
     *
     * @var oxModule
     */
    private $_oModule = null;

    /**
     * Missing module files list.
     *
     * @var array
     */
    private $_aMissingFiles = array();

    /**
     * Shop directory where modules are stored.
     *
     * @var string
     */
    private $_sPathToModuleDirectory = null;

    /**
     * Return module which files are validating.
     *
     * @return oxModule
     */
    public function getModule()
    {
        return $this->_oModule;
    }

    /**
     * Set module to validate.
     *
     * @param oxModule $oModule
     */
    public function setModule(oxModule $oModule)
    {
        $this->_oModule = $oModule;
    }

    /**
     * @return string
     */
    public function getPathToModuleDirectory()
    {
        if (is_null($this->_sPathToModuleDirectory)) {
            $this->setPathToModuleDirectory(oxRegistry::getConfig()->getModulesDir());
        }
        return $this->_sPathToModuleDirectory;
    }

    /**
     * @param string $sModuleDirectory
     */
    public function setPathToModuleDirectory($sModuleDirectory)
    {
        $this->_sPathToModuleDirectory = $sModuleDirectory;
    }

    /**
     * Validates module files.
     * Return true if module files exists.
     * Return false if at least one module file does not exist.
     *
     * @return bool
     */
    public function validate()
    {
        $blModuleValid = $this->_allModuleExtensionsExists();
        $blModuleValid = $this->_allModuleFilesExists() && $blModuleValid;

        return $blModuleValid;
    }

    /**
     * Get missing files which result to invalid module.
     *
     * @return array
     */
    public function getMissingFiles()
    {
        return $this->_aMissingFiles;
    }

    /**
     * Return true if all module files which extends shop class exists.
     *
     * @return bool
     */
    protected function _allModuleExtensionsExists()
    {
        $oModule = $this->getModule();
        $aModuleExtendedFiles = $oModule->getExtensions();
        $blAllModuleExtensionsExists = $this->_allFilesExists($aModuleExtendedFiles, true);
        return $blAllModuleExtensionsExists;
    }

    /**
     * Return true if all module independent PHP files exist.
     *
     * @return mixed
     */
    protected function _allModuleFilesExists()
    {
        $oModule = $this->getModule();
        $aModuleExtendedFiles = $oModule->getFiles();
        $blAllModuleFilesExists = $this->_allFilesExists($aModuleExtendedFiles);
        return $blAllModuleFilesExists;

    }

    /**
     * Return true if all requested file exists.
     *
     * @param array $aModuleExtendedFiles of files which must exist.
     * @param bool $blAddExtension if add .php extension to checked files.
     *
     * @return bool
     */
    private function _allFilesExists($aModuleExtendedFiles, $blAddExtension = false)
    {
        $blAllModuleFilesExists = true;
        foreach ($aModuleExtendedFiles as $sModuleName => $sModulePath) {
            $sExtPath = $this->getPathToModuleDirectory() . $sModulePath;
            if ($blAddExtension) {
                $sExtPath .= '.php';
            }
            if (!file_exists($sExtPath)) {
                $blAllModuleFilesExists = false;
                $this->_aMissingFiles[$sModuleName] = $sModulePath;
            }
        }
        return $blAllModuleFilesExists;
    }
}