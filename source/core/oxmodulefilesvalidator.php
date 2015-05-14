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
 * Module files validator class.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class oxModuleFilesValidator implements oxIModuleValidator
{

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
     * Gets path to module directory.
     *
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
     * Sets path to module directory.
     *
     * @param string $sPathToModuleDirectory
     */
    public function setPathToModuleDirectory($sPathToModuleDirectory)
    {
        $this->_sPathToModuleDirectory = $sPathToModuleDirectory;
    }

    /**
     * Validates module files.
     * Return true if module files exists.
     * Return false if at least one module file does not exist.
     *
     * @param oxModule $oModule object to validate metadata.
     *
     * @return bool
     */
    public function validate(oxModule $oModule)
    {
        $this->_resetMissingFiles();
        $blModuleValid = $this->_allModuleExtensionsExists($oModule);
        $blModuleValid = $this->_allModuleFilesExists($oModule) && $blModuleValid;

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
     * Resets missing files array.
     */
    protected function _resetMissingFiles()
    {
        $this->_aMissingFiles = array();
    }

    /**
     * Return true if all module files which extends shop class exists.
     *
     * @param oxModule $oModule object to validate metadata.
     *
     * @return bool
     */
    protected function _allModuleExtensionsExists($oModule)
    {
        $aModuleExtendedFiles = $oModule->getExtensions();
        $blAllModuleExtensionsExists = $this->_allFilesExists($aModuleExtendedFiles, true, 'extensions');

        return $blAllModuleExtensionsExists;
    }

    /**
     * Return true if all module independent PHP files exist.
     *
     * @param oxModule $oModule object to validate metadata.
     *
     * @return mixed
     */
    protected function _allModuleFilesExists($oModule)
    {
        $aModuleExtendedFiles = $oModule->getFiles();
        $blAllModuleFilesExists = $this->_allFilesExists($aModuleExtendedFiles);

        return $blAllModuleFilesExists;

    }

    /**
     * Return true if all requested file exists.
     *
     * @param array  $aModuleExtendedFiles of files which must exist.
     * @param bool   $blAddExtension       if add .php extension to checked files.
     * @param string $sListName            if add .php extension to checked files.
     *
     * @return bool
     */
    private function _allFilesExists($aModuleExtendedFiles, $blAddExtension = false, $sListName = 'files')
    {
        $blAllModuleFilesExists = true;
        foreach ($aModuleExtendedFiles as $sModuleName => $sModulePath) {
            $sPathToModuleDirectory = $this->getPathToModuleDirectory();
            $sPathToModuleDirectory = $this->_addDirectorySeparatorAtTheEndIfNeeded($sPathToModuleDirectory);
            $sExtPath = $sPathToModuleDirectory . $sModulePath;
            if ($blAddExtension) {
                $sExtPath .= '.php';
            }
            if (!file_exists($sExtPath)) {
                $blAllModuleFilesExists = false;
                $this->_aMissingFiles[$sListName][$sModuleName] = $sModulePath;
            }
        }

        return $blAllModuleFilesExists;
    }

    /**
     * Check if path has directory separator at the end. Add it if needed.
     *
     * @param strig $sPathToModuleDirectory Module directory pat
     *
     * @return string
     */
    private function _addDirectorySeparatorAtTheEndIfNeeded($sPathToModuleDirectory)
    {
        if (substr($sPathToModuleDirectory, -1) != DIRECTORY_SEPARATOR) {
            $sPathToModuleDirectory .= DIRECTORY_SEPARATOR;

            return $sPathToModuleDirectory;
        }

        return $sPathToModuleDirectory;
    }
}
