<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

/**
 * Generates class chains for extended classes by modules.
 * IMPORTANT: Due to the way the shop is prepared for testing, you must not use Registry::getConfig() in this class.
 *            oxNew will enter in an endless loop, if you try to do that.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleChainsGenerator
{
    /** @var \OxidEsales\Eshop\Core\Module\ModuleVariablesLocator */
    private $moduleVariablesLocator;

    /**
     * @param \OxidEsales\Eshop\Core\Module\ModuleVariablesLocator $moduleVariablesLocator
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
        $activeChain = $this->getActiveChain($className, $classAlias);
        if (!empty($activeChain)) {
            $className = $this->createClassExtensions($activeChain, $classAlias);
        }

        return $className;
    }

    /**
     * Assembles class chains.
     *
     * @param string $className  Class name.
     * @param string $classAlias Class alias, used for searching module extensions. Class is used if no alias given.
     *
     * @return array
     */
    public function getActiveChain($className, $classAlias = null)
    {
        if (!$classAlias) {
            $classAlias = $className;
        }
        $fullChain = $this->getFullChain($className, $classAlias);
        $activeChain = [];
        if (!empty($fullChain)) {
            $activeChain = $this->filterInactiveExtensions($fullChain);
        }

        return $activeChain;
    }

    /**
     * Build full class chain.
     *
     * @param string $className
     * @param string $classAlias
     *
     * @return array
     */
    public function getFullChain($className, $classAlias)
    {
        $fullChain = [];
        $lowerCaseClassAlias = strtolower($classAlias);
        $lowerCaseClassName = strtolower($className);

        $variablesLocator = $this->getModuleVariablesLocator();
        $modules = $this->getModulesArray($variablesLocator);
        $modules = array_change_key_case($modules);
        $allExtendedClasses = array_keys($modules);
        $currentExtendedClasses = array_intersect($allExtendedClasses, [$lowerCaseClassName, $lowerCaseClassAlias]);
        if (!empty($currentExtendedClasses)) {
            /*
             * there may be 2 class chains, matching the same class:
             * - one for the class alias like 'oxUser' - metadata v1.1
             * - another for the real class name like 'OxidEsales\Eshop\Application\Model\User' - metadata v1.2
             * These chains must be merged in the same order as they appear in the modules array
             */
            $classChains = [];
            /* Get the position of the class name */
            if (false !== $position = array_search($lowerCaseClassName, $allExtendedClasses)) {
                $classChains[$position] = explode("&", $modules[$lowerCaseClassName]);
            }
            /* Get the position of the alias class name */
            if (false !== $position = array_search($lowerCaseClassAlias, $allExtendedClasses)) {
                $classChains[$position] = explode("&", $modules[$lowerCaseClassAlias]);
            }

            /* Notice that the array keys will be ordered, but do not necessarily start at 0 */
            ksort($classChains);
            $fullChain = [];
            if (1 === count($classChains)) {
                /**
                 * @var array $fullChain uses the one and only element of the array
                 */
                $fullChain = reset($classChains);
            }
            if (2 === count($classChains)) {
                /**
                 * @var array $fullChain merges the first and then the second array from the $classChains
                 */
                $fullChain = array_merge(reset($classChains), next($classChains));
            }
        }

        return $fullChain;
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
        $disabledModules = $this->getDisabledModuleIds();

        foreach ($disabledModules as $disabledModuleId) {
            $classChain = $this->cleanModuleFromClassChain($disabledModuleId, $classChain);
        }

        return $classChain;
    }

    /**
     * Clean classes from chain for given module id.
     * Classes might be in module chain by path (old way) or by module namespace(new way).
     * This function removes all classes from class chain for classes inside a deactivated module's directory.
     *
     * @param string $moduleId
     * @param array  $classChain
     *
     * @return array
     */
    public function cleanModuleFromClassChain($moduleId, array $classChain)
    {
        $variablesLocator = $this->getModuleVariablesLocator();
        $registeredExtensions = $variablesLocator->getModuleVariable('aModuleExtensions');

        $toBeRemovedFromChain = [];
        if (isset($registeredExtensions[$moduleId])) {
            $toBeRemovedFromChain = array_combine($registeredExtensions[$moduleId], $registeredExtensions[$moduleId]);
        }

        foreach ($classChain as $key => $moduleClass) {
            if (isset($toBeRemovedFromChain[$moduleClass])) {
                unset($classChain[$key]);
            }
        }

        return $classChain;
    }

    /**
     * Get Ids of all deactivated module.
     * If none are deactivated, returns an empty array.
     *
     * @return array
     */
    public function getDisabledModuleIds()
    {
        $variablesLocator = $this->getModuleVariablesLocator();
        $disabledModules = $variablesLocator->getModuleVariable('aDisabledModules');
        $disabledModules = is_array($disabledModules) ? $disabledModules : [];

        return $disabledModules;
    }

    /**
     * SPIKE: extract function to match moduleId with installation path
     *        Example: aModulePaths = array('MyTestModule' => 'myvendor/mymodule',
     *                                      'oepaypal'     => 'oe/oepaypal')
     *
     * TODD: Think about case sensitivity issues
     *
     * Get module path relative to source/modules for given module id.
     *
     * @param string $moduleId
     *
     * @return string
     */
    public function getModuleDirectoryByModuleId($moduleId)
    {
        $variablesLocator = $this->getModuleVariablesLocator();
        $modulePaths = $variablesLocator->getModuleVariable('aModulePaths');

        $moduleDirectory = $moduleId;
        if (is_array($modulePaths) && array_key_exists($moduleId, $modulePaths)) {
            if (isset($modulePaths[$moduleId])) {
                $moduleDirectory = $modulePaths[$moduleId];
            }
        }

        return $moduleDirectory;
    }

    /**
     * Creates middle classes if needed.
     *
     * @param array  $classChain Module names
     * @param string $baseClass  Oxid base class
     *
     * @throws \OxidEsales\Eshop\Core\Exception\SystemComponentException missing system component exception
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
                if (\OxidEsales\Eshop\Core\NamespaceInformationProvider::isNamespacedClass($extensionPath)) {
                    $parentClass = $extensionPath;
                    $lastClass = $extensionPath;
                } else {
                    $parentClass = basename($extensionPath);
                    $lastClass = basename($extensionPath);
                }
            }
        }

        //returning the last module from the chain
        return $lastClass;
    }

    /**
     * Checks, if a given class can be loaded and create an alias for _parent.
     * If the class cannot be loaded, some error handling is done.
     *
     * @see self::onModuleExtensionCreationError
     * @see self::handleSpecialCases
     *
     * e.g. class suboutput1_parent extends oxoutput {}
     *      class suboutput2_parent extends suboutput1 {}
     *
     * @param string $parentClass
     * @param string $moduleClass
     *
     * @throws \OxidEsales\Eshop\Core\Exception\SystemComponentException
     *
     * @return bool Return on error
     */
    protected function createClassExtension($parentClass, $moduleClass)
    {
        if (!\OxidEsales\Eshop\Core\NamespaceInformationProvider::isNamespacedClass($moduleClass)) {
            return $this->backwardsCompatibleCreateClassExtension($parentClass, $moduleClass);
        }

        /**
         * Test if the class file could be loaded
         */
        /** @var \Composer\Autoload\ClassLoader $composerClassLoader */
        $composerClassLoader = include VENDOR_PATH . 'autoload.php';
        if (!$this->isUnitTest() && // In unit test some classes are created dynamically, so the files would not exist :-(
            !strpos($moduleClass, '_parent') &&
            !$composerClassLoader->findFile($moduleClass)) {
            $this->handleSpecialCases($parentClass);
            $this->onModuleExtensionCreationError($moduleClass);

            return false;
        }

        if (!class_exists($moduleClass, false)) {
            $moduleClassParentAlias = $moduleClass . "_parent";
            if (!class_exists($moduleClassParentAlias, false)) {
                class_alias($parentClass, $moduleClassParentAlias);
            }
        }

        return true;
    }

    /**
     * Backwards compatible self::createClassExtension
     *
     * @param string $parentClass     Name of the parent class
     * @param string $moduleClassPath Path of the module class as it is defined in metadata.php 'extend' section.
     *                                This is not a valid file system path
     *
     * @return bool
     *
     * @deprecated since v6.0 (2017-03-14); This method will be removed in the future.
     */
    private function backwardsCompatibleCreateClassExtension($parentClass, $moduleClassPath)
    {
        $moduleClass = basename($moduleClassPath);
        /**
         * Due to the way the shop is prepared for testing, you must not use Registry::getConfig() in this class.
         * So do not try to get "sShopDir" like this:
         * $modulesDirectory = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam("sShopDir");
         */
        $modulesDirectory = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class)->getVar("sShopDir");
        $moduleClassFile = "$modulesDirectory/modules/$moduleClassPath.php";
        $moduleClassParentAlias = $moduleClass . "_parent";

        /**
         * Test if the class file could be read
         */
        if (!$this->isUnitTest() && // In unit test some classes are created dynamically, so the files would not exist :-(
            !is_readable($moduleClassFile)) {
            $this->handleSpecialCases($parentClass);
            $this->onModuleExtensionCreationError($moduleClass);

            return false;
        }

        if (!class_exists($moduleClass, false)) {
            /**
             * Create parent alias before trying to load the module class as the class extends this alias
             */
            if (!class_exists($moduleClassParentAlias, false)) {
                class_alias($parentClass, $moduleClassParentAlias);
            }
            include_once $moduleClassFile;

            /**
             * Test if the class could be loaded
             */
            if (!class_exists($moduleClass)) {
                $this->handleSpecialCases($parentClass);
                $this->onModuleExtensionCreationError($moduleClassPath);

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
     */
    protected function handleSpecialCases($requestedClass)
    {
        if (($requestedClass == "oxconfig") || ($requestedClass == \OxidEsales\Eshop\Core\Config::class)) {
            $config = new \OxidEsales\Eshop\Core\Config();
            \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $config);
        }
    }

    /**
     * If blDoNotDisableModuleOnError config value is false, disables bad module.
     * To avoid problems with unit tests it only throw an exception if class does not exist.
     *
     * @param string $moduleClass
     *
     * @throws \OxidEsales\EshopCommunity\Core\Exception\SystemComponentException
     */
    protected function onModuleExtensionCreationError($moduleClass)
    {
        $disableModuleOnError = !$this->getConfigBlDoNotDisableModuleOnError();
        if ($disableModuleOnError) {
            if ($this->disableModule($moduleClass)) {
                /**
                 * The business logic does allow to throw an exception here, but just at least the disabling of the
                 * module must be logged
                 */
                $module = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);
                $moduleId = $module->getIdByPath($moduleClass);
                $message = sprintf('Module class %s not found. Module ID %s disabled', $moduleClass, $moduleId);
                $exception = new \OxidEsales\Eshop\Core\Exception\SystemComponentException($message);
                \OxidEsales\Eshop\Core\Registry::getLogger()->error($exception->getMessage(), [$exception]);
            }
        } else {
            $exception =  new \OxidEsales\Eshop\Core\Exception\SystemComponentException();
            /** Use setMessage here instead of passing it in constructor in order to test exception message */
            $exception->setMessage('EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND' . ' ' . $moduleClass);
            $exception->setComponent($moduleClass);

            throw $exception;
        }
    }

    /**
     * Disables module, adds to aDisabledModules config.
     *
     * @param string $modulePath Full module path
     *
     * @return bool
     */
    public function disableModule($modulePath)
    {
        $module = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);
        $moduleId = $module->getIdByPath($modulePath);
        $module->load($moduleId);

        $moduleCache = oxNew('oxModuleCache', $module);
        $moduleInstaller = oxNew('oxModuleInstaller', $moduleCache);

        return $moduleInstaller->deactivate($module);
    }

    /**
     * Getter for ModuleVariablesLocator.
     *
     * @return \OxidEsales\Eshop\Core\Module\ModuleVariablesLocator
     */
    public function getModuleVariablesLocator()
    {
        return $this->moduleVariablesLocator;
    }

    /**
     * Getter for module array.
     *
     * @param \OxidEsales\Eshop\Core\Module\ModuleVariablesLocator $variablesLocator
     *
     * @return array
     */
    protected function getModulesArray(\OxidEsales\Eshop\Core\Module\ModuleVariablesLocator $variablesLocator)
    {
        $modules = (array) $variablesLocator->getModuleVariable('aModules');

        return $modules;
    }

    /**
     * @return mixed
     */
    protected function getConfigBlDoNotDisableModuleOnError()
    {
        return \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class)->getVar("blDoNotDisableModuleOnError");
    }

    /**
     * Conveniance method for tests
     *
     * @return bool
     */
    protected function isUnitTest()
    {
        return defined('OXID_PHP_UNIT');
    }
}
