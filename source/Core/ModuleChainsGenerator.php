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

namespace OxidEsales\Eshop\Core;

use oxRegistry;
use oxConfig;

/**
 * Generates class chains for extended classes by modules.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class ModuleChainsGenerator
{
    /** @var ModuleVariablesLocator */
    private $moduleVariablesLocator;

    /**
     * @var $extensionWithEror array of moduleExtensions that can not be loaded
     * Used within onModuleExtensionCreationError method
     * it is needed to collect errors while already handling errors
     */
    protected $extensionWithError = [];

    /**
     * @var $handlingError boolean holding the information if error handling is on going
     * if it is set to true errors are collected but handling is postponed until 
     * the current error was handled. See also $extensionWithError
     */
    protected $handlingError = false;

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
     * @param string $class      Class name.
     * @param string $classAlias Class alias, used for searching module extensions. Class is used if no alias given.
     *
     * @return string
     */
    public function createClassChain($class, $classAlias = null)
    {
        if (!$classAlias) {
            $classAlias = $class;
        }

        $variablesLocator = $this->getModuleVariablesLocator();
        $modules = (array) $variablesLocator->getModuleVariable('aModules');
        $modules = array_change_key_case($modules);

        if (array_key_exists($classAlias, $modules)) {
            $fullChain = explode("&", $modules[$classAlias]);
            $activeChain = $this->filterInactiveExtensions($fullChain);

            if (!empty($activeChain)) {
                $class = $this->createClassExtensions($activeChain, $class);
            }
        }

        return $class;
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
                $lastClass = $parentClass;
            } else {
                print "Debug: failed to load $extensionPath (parent $parentClass)";
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

        return true;
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
     * @param string $classExtension the path of the class
     * @param string $moduleClass
     *
     * @throws \oxSystemComponentException
     * @return void
     */
    protected function onModuleExtensionCreationError($classExtension, $moduleClass)
    {
        $this->extensionWithError[$classExtension] = $moduleClass;
        if ($this->handlingError) {
            //avoid endless loop in case error handling may use indirectly $classExtension  
            return;
        }
        $this->handlingError = true;
        
        do{
           $this->handleModuleExtensionError($classExtension, $moduleClass);
           unset($this->extensionWithError[$classExtension]);
           $classExtension = key($this->extensionWithError);
           $moduleClass = $this->extensionWithError[$classExtension];
        } while($classExtension);

        $this->handlingError = false;
     }
    
     protected function handleModuleExtensionError($classExtension, $moduleClass)
     {
  
        //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //TODO: try to solve the error by fixing the internal module state:
        // so at least the next request will get the correct extension chain
        // Only if automatically fixing is not possible one of the other options(e.g. exception, disabling module) should be used
        //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        
        $oxConfigFile = oxRegistry::get("oxConfigFile");
        $blIgnoreModuleExtensionError = $oxConfigFile->getVar("blIgnoreExtensionOnError");
        $exception = oxNew("oxSystemComponentException");
        $exception->setMessage("EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND");
        $exception->setComponent($moduleClass);
        if ($blIgnoreModuleExtensionError) {
            //ignoring the error while building the module chain is important to keep the system usable
            //so admin can run console commands or use the admin area to fix/analyze the problem.
            //But writing the error to the logfile is the most important thing so someone will be able to analyse
            //whats cause maybe unexpected behavior in case of an broken module chain
            $exception->debugOut();
        } else {
            $disableModuleOnError = !$oxConfigFile->getVar("blDoNotDisableModuleOnError");
            if ($disableModuleOnError) {
                //it is important to log error to make clear that something went wrong
                //and make it it possible to analyze the problem
                $exception->debugOut();
                //disabling the module because if one part of the module is not working the shop might work better without that module at all
                $this->disableModule($classExtension);
            } else {
                throw $exception;
            }
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
}
