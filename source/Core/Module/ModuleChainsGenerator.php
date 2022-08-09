<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ActiveClassExtensionChainResolverInterface;

/**
 * Generates class chains for extended classes by modules.
 * IMPORTANT: Due to the way the shop is prepared for testing, you must not use Registry::getConfig() in this class.
 *            oxNew will enter in an endless loop, if you try to do that.
 *
 * @internal Do not make a module extension for this class.
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
        $chain = $this->getClassExtensionChain($this->getModuleVariablesLocator());
        $classChain = $chain[$className] ?? [];

        return $classAlias && $classAlias !== $className
            ? array_merge($classChain, $this->getChainForBackwardsCompatibilityClassAlias($chain, $classAlias))
            : $classChain;
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
        /**
         * Test if the class file could be loaded
         */
        /** @var \Composer\Autoload\ClassLoader $composerClassLoader */
        $composerClassLoader = include VENDOR_PATH . 'autoload.php';
        if (
            !$this->isUnitTest() && // In unit test some classes are created dynamically, so the files would not exist :-(
            !strpos($moduleClass, '_parent') &&
            !$composerClassLoader->findFile($moduleClass)
        ) {
            $this->handleSpecialCases($parentClass);
            $this->onModuleExtensionCreationError($moduleClass);

            return false;
        }

        $moduleClassParentAlias = $moduleClass . "_parent";
        if (!class_exists($moduleClassParentAlias, false)) {
            class_alias($parentClass, $moduleClassParentAlias);
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
            $module = new \OxidEsales\Eshop\Core\Module\Module();
            $moduleId = $module->getIdByPath($moduleClass);
        }
        $message = sprintf('Module class %s not found. Module ID %s', $moduleClass, $moduleId);
        $exception = new \OxidEsales\Eshop\Core\Exception\SystemComponentException($message);
        \OxidEsales\Eshop\Core\Registry::getLogger()->error($exception->getMessage(), [$exception]);
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
        return (array) $variablesLocator->getModuleVariable('aModules');
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

    private function getChainForBackwardsCompatibilityClassAlias(array $chain, string $classAlias): array
    {
        return array_change_key_case($chain)[strtolower($classAlias)] ?? [];
    }
}
