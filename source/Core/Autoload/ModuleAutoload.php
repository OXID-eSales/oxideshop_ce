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
namespace OxidEsales\EshopCommunity\Core\Autoload;

use OxidEsales\Eshop\Core\Registry;

/**
 * Autoloader for module classes and extensions.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleAutoload
{
    /** @var array Classes, for which extension class chain was created. */
    public $triedClasses = array();

    /**
     * @var null|ModuleAutoload A singleton instance of this class or a sub class of this class
     */
    private static $instance = null;

    /**
     * ModuleAutoload constructor.
     *
     * Make constructor protected to ensure Singleton pattern
     */
    protected function __construct()
    {
    }

    /**
     * Magic clone method.
     *
     * Make method private to ensure Singleton pattern
     */
    private function __clone()
    {
    }

    /**
     * Magic wakeup method.
     *
     * Make method private to ensure Singleton pattern
     */
    private function __wakeup()
    {
    }

    /**
     * Tries to autoload given class. If class was not found in module files array,
     * checks module extensions.
     *
     * @param string $class Class name.
     *
     * @return bool
     */
    public static function autoload($class)
    {
        /**
         * Classes from unified namespace canot be loaded by this auto loader.
         * Do not try to load them in order to avoid strange errors in edge cases.
         */
        if (false !== strpos($class, 'OxidEsales\Eshop\\')) {
            return false;
        }
        $instance = static::getInstance();

        $class = strtolower(basename($class));

        if ($classPath = $instance->getFilePath($class)) {
            include $classPath;
        } else {
            $class = preg_replace('/_parent$/i', '', $class);

            if (!in_array($class, $instance->triedClasses)) {
                $instance->triedClasses[] = $class;
                $instance->createExtensionClassChain($class);
            }
        }
    }

    /**
     * Returns the singleton instance of this class or of a sub class of this class.
     *
     * @return ModuleAutoload The singleton instance.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Tries to find file path for given class. Returns empty string on path not found.
     *
     * @param string $class
     *
     * @return string
     */
    protected function getFilePath($class)
    {
        $filePath = '';

        $moduleFiles = Registry::getUtilsObject()->getModuleVar('aModuleFiles');
        if (is_array($moduleFiles)) {
            $basePath = getShopBasePath();
            foreach ($moduleFiles as $moduleId => $classPaths) {
                if (array_key_exists($class, $classPaths)) {
                    $moduleFilePath = $basePath . 'modules/' . $classPaths[$class];
                    if (file_exists($moduleFilePath)) {
                        $filePath = $moduleFilePath;
                    }
                }
            }
        }

        return $filePath;
    }

    /**
     * When module is extending other module's extension (module class, which is extending shop class),
     * this class comes to autoload and class chain has to be created.
     *
     * @param string $class
     */
    protected function createExtensionClassChain($class)
    {
        $utilsObject = Registry::getUtilsObject();

        $extensions = $utilsObject->getModuleVar('aModules');
        if (is_array($extensions)) {
            $class = preg_quote($class, '/');

            foreach ($extensions as $parentClass => $extensionPath) {
                if (preg_match('/\b' . $class . '($|\&)/i', $extensionPath)) {
                    $utilsObject->getClassName($parentClass);
                    break;
                }
            }
        }
    }
}
