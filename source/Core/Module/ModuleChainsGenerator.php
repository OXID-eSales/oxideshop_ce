<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use Psr\Container\ContainerInterface;

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
        return $this->getFullChain($className, $classAlias);
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
        $modules = $this->getClassExtensionChain($variablesLocator);
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
     * @deprecated since v6.4 (2019-05-22); There are only extensions of active modules in the class chain.
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
     * @deprecated since v6.4 (2019-05-22); If you want to clean a module from the class chain, deactivate the module.
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
     * @deprecated since v6.4 (2019-05-22); Use ShopConfigurationDaoBridgeInterface instead to get inactive modules.
     *
     * @return array
     */
    public function getDisabledModuleIds()
    {
        $moduleStateService = $this->getModuleStateService();
        $moduleConfigurations = $this->getShopConfigurationDaoBridge()
                                     ->get()
                                     ->getModuleConfigurations();

        $disabledModuleIds = [];

        foreach ($moduleConfigurations as $moduleConfiguration) {
            if (!$moduleStateService->isActive($moduleConfiguration->getId(), Registry::getConfig()->getShopId())) {
                $disabledModuleIds[] = $moduleConfiguration->getId();
            }
        }

        return $disabledModuleIds;
    }

    /**
     * Get module path relative to source/modules for given module id.
     *
     * @deprecated since v6.4 (2019-05-22); Use ShopConfigurationDaoBridgeInterface instead.
     *
     * @param string $moduleId
     *
     * @return string
     */
    public function getModuleDirectoryByModuleId($moduleId)
    {
        try {
            $moduleConfiguration = $this->getModuleConfigurationDaoBridge()->get($moduleId);
            $moduleDirectory = $moduleConfiguration->getPath();
        } catch (ModuleConfigurationNotFoundException $domainException) {
            return $moduleId;
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
            if (!class_exists($moduleClass, false)) {
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
        // We do actually have to check the whole inheritance chain in case two OXID modules each have an extension
        // on oxconfig. Checking for $requestedClass only would cover only one inheritance step.
        
        $isConfigClass = false;
        $currentClass = $requestedClass;
        $safetyCount = 0;
        do {
            if (($currentClass == "oxconfig") || ($currentClass == \OxidEsales\Eshop\Core\Config::class)) {
                $isConfigClass = true;
                break;
            }

            if ($safetyCount++ === 200) {
                throw new \OxidEsales\Eshop\Core\Exception\SystemComponentException('Recursion limit reached while traversing class inheritance chain.');
            }

            // We can be sure that the parent class of the current class is actually defined due to the way
            // the extension chain is traversed.
        } while ($currentClass = get_parent_class($currentClass));

        if ($isConfigClass) {
            $config = new \OxidEsales\Eshop\Core\Config();
            \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $config);
        }
    }

    /**
     * Writes/logs an error on module extension creation problem
     *
     * @param string $moduleClass
     */
    protected function onModuleExtensionCreationError($moduleClass)
    {
        $moduleId = "(module id not availible)";
        if (class_exists("\OxidEsales\Eshop\Core\Module\Module", false)) {
            $module = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);
            $moduleId = $module->getIdByPath($moduleClass);
        }
        $message = sprintf('Module class %s not found. Module ID %s', $moduleClass, $moduleId);
        $exception = new \OxidEsales\Eshop\Core\Exception\SystemComponentException($message);
        \OxidEsales\Eshop\Core\Registry::getLogger()->error($exception->getMessage(), [$exception]);
    }

    /**
     * If the module is found in configuration, return value is always true. Independent if the module was in a active
     * or inactive state previously.
     *
     * @deprecated since v6.4.0 (2019-05-20). Use ModuleActivationServiceInterface instead.
     *
     * @param string $modulePath Full module path
     *
     * @return bool
     */
    public function disableModule($modulePath)
    {
        $module = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);
        $moduleId = $module->getIdByPath($modulePath);

        if (false === $module->load($moduleId)) {
            return false;
        }

        try {
            $this
                ->getModuleActivationBridge()
                ->deactivate(
                    $moduleId,
                    Registry::getConfig()->getShopId()
                );
        } catch (ModuleSetupException $moduleSetupException) {
            return true;
        }

        return true;
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
     * Only classes of active modules are considered.
     *
     * @param \OxidEsales\Eshop\Core\Module\ModuleVariablesLocator $variablesLocator
     *
     * @return array
     */
    protected function getClassExtensionChain(\OxidEsales\Eshop\Core\Module\ModuleVariablesLocator $variablesLocator)
    {
        $modules = (array) $variablesLocator->getModuleVariable('aModules');

        return $modules;
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

    /**
     * @return ModuleActivationBridgeInterface
     */
    private function getModuleActivationBridge(): ModuleActivationBridgeInterface
    {
        return $this->getContainer()
                    ->get(ModuleActivationBridgeInterface::class);
    }

    /**
     * @return ModuleConfigurationDaoBridgeInterface
     */
    private function getModuleConfigurationDaoBridge() : ModuleConfigurationDaoBridgeInterface
    {
        return $this->getContainer()
                    ->get(ModuleConfigurationDaoBridgeInterface::class);
    }

    /**
     * @return ShopConfigurationDaoBridgeInterface
     */
    private function getShopConfigurationDaoBridge() : ShopConfigurationDaoBridgeInterface
    {
        return $this->getContainer()
                    ->get(ShopConfigurationDaoBridgeInterface::class);
    }

    /**
     * @return ModuleStateServiceInterface
     */
    private function getModuleStateService() : ModuleStateServiceInterface
    {
        return $this->getContainer()->get(ModuleStateServiceInterface::class);
    }

    /**
     * @return ContainerInterface
     */
    private function getContainer() : ContainerInterface
    {
        return ContainerFactory::getInstance()->getContainer();
    }
}
