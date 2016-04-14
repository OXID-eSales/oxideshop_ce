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

namespace OxidEsales\Eshop\Core\Module;

use oxModule;

/**
 * Class responsible for cleaning not used extensions for module which is going to be activated.
 *
 * @package  OxidEsales\Eshop\Core\Module
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class ModuleExtensionsCleaner
{
    /**
     * Removes garbage ( module not used extensions ) from all installed extensions list.
     * For example: some classes were renamed, so these should be removed.
     *
     * @param array    $aInstalledExtensions Installed extensions
     * @param oxModule $oModule              Module
     *
     * @return array
     */
    public function cleanExtensions($aInstalledExtensions, oxModule $oModule)
    {
        $aModuleExtensions = $oModule->getExtensions();

        $aInstalledModuleExtensions = $this->filterModuleArray($aInstalledExtensions, $oModule->getId());

        if (count($aInstalledModuleExtensions)) {
            $aGarbage = $this->getModuleExtensionsGarbage($aModuleExtensions, $aInstalledModuleExtensions);

            if (count($aGarbage)) {
                $aInstalledExtensions = $this->removeGarbage($aInstalledExtensions, $aGarbage);
            }
        }

        return $aInstalledExtensions;
    }

    /**
     * Filter module array using module id
     *
     * Returns active module extensions
     *
     * @param array  $aModules  Module array (nested format)
     * @param string $sModuleId Module id/folder name
     *
     * @return array
     */
    protected function filterModuleArray($aModules, $sModuleId)
    {
        $aModulePaths = \oxRegistry::getConfig()->getConfigParam('aModulePaths');
        $sPath = $aModulePaths[$sModuleId];
        if (!$sPath) {
            $sPath = $sModuleId . "/";
        }

        $aFilteredModules = array();
        foreach ($aModules as $sClass => $aExtend) {
            foreach ($aExtend as $sExtendPath) {
                if (strpos($sExtendPath, $sPath) === 0) {
                    $aFilteredModules[$sClass][] = $sExtendPath;
                }
            }
        }

        return $aFilteredModules;
    }

    /**
     * Returns extension which is no longer in metadata - garbage
     *
     * @param array $aModuleMetaDataExtensions  extensions defined in metadata.
     * @param array $aModuleInstalledExtensions extensions which are installed
     *
     * @return array
     */
    protected function getModuleExtensionsGarbage($aModuleMetaDataExtensions, $aModuleInstalledExtensions)
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
    protected function removeGarbage($aInstalledExtensions, $aGarbage)
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
}
