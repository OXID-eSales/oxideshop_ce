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

use oxModule;

/**
 * Class responsible for cleaning not used extensions for module which is going to be activated.
 *
 * @package  OxidEsales\EshopCommunity\Core\Module
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleExtensionsCleaner
{
    /**
     * Removes garbage ( module not used extensions ) from all installed extensions list.
     * For example: some classes were renamed, so these should be removed.
     *
     * @param array    $installedExtensions
     * @param oxModule $module
     *
     * @return array
     */
    public function cleanExtensions($installedExtensions, oxModule $module)
    {
        $moduleExtensions = $module->getExtensions();

        $installedModuleExtensions = $this->filterExtensionsByModuleId($installedExtensions, $module->getId());

        if (count($installedModuleExtensions)) {
            $garbage = $this->getModuleExtensionsGarbage($moduleExtensions, $installedModuleExtensions);

            if (count($garbage)) {
                $installedExtensions = $this->removeGarbage($installedExtensions, $garbage);
            }
        }

        return $installedExtensions;
    }

    /**
     * Returns extensions list by module id.
     *
     * @param array  $modules  Module array (nested format)
     * @param string $moduleId Module id/folder name
     *
     * @return array
     */
    protected function filterExtensionsByModuleId($modules, $moduleId)
    {
        $modulePaths = \oxRegistry::getConfig()->getConfigParam('aModulePaths');
        $path = $modulePaths[$moduleId];
        // TODO: This condition should be removed. Need to check integration tests.
        if (!$path) {
            $path = $moduleId . "/";
        }

        $filteredModules = array();
        foreach ($modules as $class => $extend) {
            foreach ($extend as $extendPath) {
                if (strpos($extendPath, $path) === 0) {
                    $filteredModules[$class][] = $extendPath;
                }
            }
        }

        return $filteredModules;
    }

    /**
     * Returns extension which is no longer in metadata - garbage
     *
     * @param array $moduleMetaDataExtensions  extensions defined in metadata.
     * @param array $moduleInstalledExtensions extensions which are installed
     *
     * @return array
     */
    protected function getModuleExtensionsGarbage($moduleMetaDataExtensions, $moduleInstalledExtensions)
    {
        $garbage = $moduleInstalledExtensions;

        foreach ($moduleMetaDataExtensions as $className => $classPath) {
            if (isset($garbage[$className])) {
                unset($garbage[$className][array_search($classPath, $garbage[$className])]);
                if (count($garbage[$className]) == 0) {
                    unset($garbage[$className]);
                }
            }
        }

        return $garbage;
    }

    /**
     * Removes garbage - not exiting module extensions, returns clean array of installed extensions
     *
     * @param array $installedExtensions all installed extensions ( from all modules )
     * @param array $garbage             extension which are not used and should be removed
     *
     * @return array
     */
    protected function removeGarbage($installedExtensions, $garbage)
    {
        foreach ($garbage as $className => $classPaths) {
            foreach ($classPaths as $sClassPath) {
                if (isset($installedExtensions[$className])) {
                    unset($installedExtensions[$className][array_search($sClassPath, $installedExtensions[$className])]);
                    if (count($installedExtensions[$className]) == 0) {
                        unset($installedExtensions[$className]);
                    }
                }
            }
        }

        return $installedExtensions;
    }
}
