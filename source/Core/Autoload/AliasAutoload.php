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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Core\Autoload;

/**
 * This is an autoloader that performs several tricks to provide class aliases
 * for the new namespaced classes.
 *
 * The aliases are provided by a class map provider. But it is not sufficient
 * to
 */
class AliasAutoload
{
    /**
     * Array map with virtual namespace class name as key, bc class name as value.
     * @var array
     */
    private $backwardsCompatibilityClassMap = [];

    /**
     *
     */
    private $virtualNameSpaceClassMap = null;

    /**
     * AliasAutoload constructor.
     */
    public function __construct()
    {
        $classMap = include_once __DIR__ . DIRECTORY_SEPARATOR . 'BackwardsCompatibilityClassMap.php';
        $this->backwardsCompatibilityClassMap = array_map('strtolower', $classMap);
    }

    /**
     * Autoload method.
     *
     * @param string $class Name of the class to be loaded
     *
     * @return bool
     */
    public function autoload($class)
    {
        $bcAlias = null;
        $virtualAlias = null;
        $realClass = null;

        if ($this->isBcAliasRequest($class)) {
            $bcAlias = $class;
            $virtualAlias = $this->getVirtualAliasForBcAlias($class);
        }

        if ($this->isVirtualClassRequest($class)) {
            $virtualAlias = $class;
            $bcAlias = $this->getBcAliasForVirtualAlias($class);
        }

        if ($virtualAlias) {
            $realClass = $this->getRealClassForVirtualAlias($virtualAlias);
        }

        /** Pass over to the next registered autoloader, if no realClass has been found for the requested className  */
        if (!$realClass) {
            return false;
        }

        /** The class  must be loaded before class_alias can be called. */
        $this->forceClassLoading($realClass);

        /** In order not to load classes twice, see if they are already declared. */
        $declaredClasses = get_declared_classes();
        if ($bcAlias && !in_array(strtolower($bcAlias), $declaredClasses)) {
            class_alias($realClass, $bcAlias);
        }
        if ($virtualAlias && !in_array(strtolower($virtualAlias), $declaredClasses)) {
            class_alias($realClass, $virtualAlias);

            /** At this point both a bcAlias and a virtualAlias would have be created successfully */
            return true;
        }

        return false;
    }

    /**
     * Return true, if the given class name is a backwards compatible alias like oxArticle
     *
     * @param string $class Name of the class
     *
     * @return bool
     */
    private function isBcAliasRequest($class)
    {
        $classMap = $this->getBackwardsCompatibilityClassMap();

        return in_array(strtolower($class), $classMap);
    }

    /**
     * Return true, if the given class name is a virtual alias like OxidEsales\Eshop\Application\Model\Article
     *
     * @param string $class Name of the class. No leading backspaces should be used here.
     *
     * @return bool
     */
    private function isVirtualClassRequest($class)
    {
        return strpos(ltrim($class, '\\'), 'OxidEsales\\Eshop\\') === 0;
    }

    /**
     * Return the name of a virtual class for a given backwards compatible class
     *
     * @param string $class Name of the backwards compatible class like oxArticle
     *
     * @return null|string Name of the virtual class like OxidEsales\Eshop\Application\Model\Article
     */
    private function getVirtualAliasForBcAlias($class)
    {
        $result = null;

        $classMap = $this->getBackwardsCompatibilityClassMap();
        if ($resolvedClassName = array_search(strtolower($class), $classMap)) {
            $result = $resolvedClassName;
        }

        return  $result;
    }

    /**
     * Return the name of a backwards compatible class for a given virtual class
     *
     * @param string $class Name of the virtual class like OxidEsales\Eshop\Application\Model\Article
     *
     * @return null|string Name of the backwards compatible class like oxArticle
     */
    private function getBcAliasForVirtualAlias($class)
    {
        $result = null;

        $classMap = $this->getBackwardsCompatibilityClassMap();
        if (key_exists(ltrim($class, '\\'), $classMap)) {
            $result = $classMap[$class];
        }

        return $result;
    }

    /**
     * Return the name of a real class for a given virtual class
     *
     * @param string $class Name of the virtual class like OxidEsales\Eshop\Application\Model\Article
     *
     * @return null|string Name of the real class like OxidEsales\EshopCommunity\Application\Model\Article
     */
    private function getRealClassForVirtualAlias($class)
    {
        $result = null;

        $virtualClassMap = $this->getVirtualClassMap();
        if (key_exists($class, $virtualClassMap)) {
            $result = $virtualClassMap[$class];
        }

        return $result;
    }

    /**
     * Load a given class using the stack of registered autoloaders.
     * If this class is the first autoloader in the stack, this is the point where recursion would start.
     *
     * @param string $class Name of the class to load
     */
    private function forceClassLoading($class)
    {
        /** Calling class_exists will trigger the corresponding autoloader */
        class_exists($class);
    }

    /**
     * Return the backwards compatibile classmap
     *
     * @return array Mapping of virtual to backwards compatibile classes
     */
    private function getBackwardsCompatibilityClassMap()
    {
        return $this->backwardsCompatibilityClassMap;
    }

    /**
     * Return the corresponding virtual class map.
     * When creating the instance of VirtualNameSpaceClassMap is is assured, that no auto loader will be triggered.
     *
     * @return array Mapping of virtual to real classes
     */
    private function getVirtualClassMap()
    {
        if (is_null($this->virtualNameSpaceClassMap)) {
            //IMPORTANT: we must use 'new' and the explicit namespace here because virtual namespace
            //           objects are not available at this moment.
            $classMapProvider = new \OxidEsales\EshopCommunity\Core\Autoload\VirtualNameSpaceClassMapProvider;
            $this->virtualNameSpaceClassMap = $classMapProvider->getClassMap();
        }
        return $this->virtualNameSpaceClassMap;
    }
}
spl_autoload_register([new AliasAutoload(), 'autoload']);
