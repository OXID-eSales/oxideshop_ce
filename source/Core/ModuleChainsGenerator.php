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

namespace OxidEsales\EshopCommunity\Core;

use oxConfig;
use oxRegistry;

/**
 * Generates class chains for extended classes by modules.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleChainsGenerator
{
    /** @var ModuleVariablesLocator */
    private $moduleVariablesLocator;

    /**
     * @param ModuleVariablesLocator $moduleVariablesLocator
     */
    public function __construct($moduleVariablesLocator)
    {
        $this->moduleVariablesLocator = $moduleVariablesLocator;
    }

    /**
     * Creates given class chains.
     *
     * @param string $className  Class name.
     * @param string $classAlias Class alias, used for searching module extensions. Class is used if no alias given.
     *
     * @return string
     */
    public function createClassChain($className, $classAlias = null)
    {
        if (!$classAlias) {
            $classAlias = $className;
        }
        $lowerCaseClassAlias = strtolower($classAlias);
        $lowerCaseClassName = strtolower($className);

        $variablesLocator = $this->getModuleVariablesLocator();
        $modules = $this->getModulesArray($variablesLocator);
        $modules = array_change_key_case($modules);
        $allExtendedClasses = array_keys($modules);
        $currentExtendedClasses = array_intersect($allExtendedClasses, [$lowerCaseClassName, $lowerCaseClassAlias]);
        if (!empty($currentExtendedClasses)) {
            /**
             * there may be 2 class chains, matching the same class:
             * - one for the class alias like 'oxUser' - metadata v1.1
             * - another for the real class name like 'OxidEsales\Eshop\Application\Model\User' - metadata v1.2
             * These chains must be merged in the same order as they appear in the modules array
             */
            $classChains = [];
            /** Get the position of the class name */
            if (false !== $position = array_search($lowerCaseClassName, $allExtendedClasses)) {
                $classChains[$position] = explode("&", $modules[$lowerCaseClassName]);
            }
            /** Get the position of the alias class name */
            if (false !== $position = array_search($lowerCaseClassAlias, $allExtendedClasses)) {
                $classChains[$position] = explode("&", $modules[$lowerCaseClassAlias]);
            }

            /** Notice that the array keys will be ordered, but do not necessarily start at 0 */
            ksort($classChains);
            $fullChain = [];
            if (1 === count($classChains)) {
                /** @var array $fullChain uses the one and only element of the array */
                $fullChain = reset($classChains);
            }
            if (2 === count($classChains)) {
                /** @var array $fullChain merges the first and then the second array from the $classChains */
                $fullChain = array_merge(reset($classChains), next($classChains));
            }

            $activeChain = $this->filterInactiveExtensions($fullChain);

            if (!empty($activeChain)) {
                $className = $this->createClassExtensions($activeChain, $classAlias);
            }
        }

        return $className;
    }

    /**
     * Checks if module is disabled, added to aDisabledModules config.
     *
     * @param array $classChain Module names
     *
     * @return array
     */
    public function filterInactiveExtensions($classChain)
    {
        $variablesLocator = $this->getModuleVariablesLocator();
        $disabledModules = $variablesLocator->getModuleVariable('aDisabledModules');
        $modulePaths = $variablesLocator->getModuleVariable('aModulePaths');

        if (is_array($disabledModules) && count($disabledModules) > 0) {
            foreach ($disabledModules as $disabledModuleId) {
                $disabledModuleDirectory = $disabledModuleId;
                if (is_array($modulePaths) && array_key_exists($disabledModuleId, $modulePaths)) {
                    if (isset($modulePaths[$disabledModuleId])) {
                        $disabledModuleDirectory = $modulePaths[$disabledModuleId];
                    }
                }
                foreach ($classChain as $key => $moduleClass) {
                    if (strpos($moduleClass, $disabledModuleDirectory . "/") === 0) {
                        unset($classChain[$key]);
                    } elseif (strpos($disabledModuleDirectory, ".")) {
                        // If module consists of one file without own dir (getting module.php as id, instead of module)
                        if (strpos($disabledModuleDirectory, strtolower($moduleClass)) === 0) {
                            unset($classChain[$key]);
                        }
                    }
                }
            }
        }

        return $classChain;
    }

    /**
     * Creates middle classes if needed.
     *
     * @param array  $classChain Module names
     * @param string $baseClass  Oxid base class
     *
     * @throws \oxSystemComponentException missing system component exception
     *
     * @return string
     */
    protected function createClassExtensions($classChain, $baseClass)
    {
        //security: just preventing string termination
        $lastClass = str_replace(chr(0), '', $baseClass);
        $parentClass = $lastClass;

        foreach ($classChain as $extensionPath) {
            $extensionPath = str_replace(chr(0), '', $extensionPath);

            if ($this->createClassExtension($parentClass, $extensionPath)) {
                $parentClass = basename($extensionPath);
                $lastClass = basename($extensionPath);
            }
        }

        //returning the last module from the chain
        return $lastClass;
    }

    /**
     * Creating middle classes
     * e.g. class suboutput1_parent extends oxoutput {}
     *      class suboutput2_parent extends suboutput1 {}
     *
     * @param string $class
     * @param string $extensionPath
     *
     * @throws \oxSystemComponentException
     *
     * @return bool
     */
    protected function createClassExtension($class, $extensionPath)
    {
        $extensionClass = basename($extensionPath);

        if (!class_exists($extensionClass, false)) {
            $extensionParentClass = $extensionClass . "_parent";

            if (!class_exists($extensionParentClass, false)) {
                class_alias($class, $extensionParentClass);
            }

            if (!$this->isNamespacedClass($extensionPath)) {
                $modulesDirectory = oxRegistry::get("oxConfigFile")->getVar("sShopDir");
                $extensionParentPath = "$modulesDirectory/modules/$extensionPath.php";

                //including original file
                if (file_exists($extensionParentPath)) {
                    include_once $extensionParentPath;
                } elseif (!class_exists($extensionClass)) {
                    $this->handleSpecialCases($class, $extensionClass);
                    $this->onModuleExtensionCreationError($extensionPath, $extensionClass);

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    private function isNamespacedClass($className)
    {
        return strpos($className, '\\') !== false;
    }

    /**
     * Special case is when oxconfig class is extended: we cant call "_disableModule" as it requires valid config object
     * but we can't create it as module class extending it does not exist. So we will use original oxConfig object instead.
     *
     * @param string $requestedClass Class, for which extension chain was generated.
     * @param string $extensionClass
     */
    protected function handleSpecialCases($requestedClass, $extensionClass)
    {
        if ($requestedClass == "oxconfig") {
            $config = new oxConfig();
            oxRegistry::set("oxConfig", $config);
        }
    }

    /**
     * If blDoNotDisableModuleOnError config value is false, disables bad module.
     * To avoid problems with unit tests it only throw an exception if class does not exist.
     *
     * @param string $classExtension
     * @param string $moduleClass
     *
     * @throws \oxSystemComponentException
     */
    protected function onModuleExtensionCreationError($classExtension, $moduleClass)
    {
        $disableModuleOnError = !oxRegistry::get("oxConfigFile")->getVar("blDoNotDisableModuleOnError");
        if ($disableModuleOnError) {
            $this->disableModule($classExtension);
        } else {
            $exception = oxNew("oxSystemComponentException");
            $exception->setMessage("EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND");
            $exception->setComponent($moduleClass);
            throw $exception;
        }
    }

    /**
     * Disables module, adds to aDisabledModules config.
     *
     * @param array $modulePath Full module path
     */
    public function disableModule($modulePath)
    {
        $module = oxNew("oxModule");
        $moduleId = $module->getIdByPath($modulePath);
        $module->load($moduleId);

        $moduleCache = oxNew('oxModuleCache', $module);
        $moduleInstaller = oxNew('oxModuleInstaller', $moduleCache);

        $moduleInstaller->deactivate($module);
    }

    /**
     * @return ModuleVariablesLocator
     */
    public function getModuleVariablesLocator()
    {
        return $this->moduleVariablesLocator;
    }

    /**
     * @param ModuleVariablesLocator $variablesLocator
     *
     * @return array
     */
    protected function getModulesArray(ModuleVariablesLocator $variablesLocator)
    {
        $modules = (array) $variablesLocator->getModuleVariable('aModules');

        return $modules;
    }
}
