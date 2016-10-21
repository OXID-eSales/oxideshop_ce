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

/**
 * Autoloader for shop classes.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ShopAutoload
{
    /** @var array List of all directories, where shop classes are located. */
    private $classDirectories = null;

    /** @var array Core classes, which paths is known and no scanning is done for them. */
    private $coreClasses = array("oxutils", "oxsupercfg", "oxutilsobject");

    /**
     * Includes file, where given class is described.
     *
     * @param string $class Class Name
     */
    public function autoload($class)
    {
        startProfile("ShopAutoload");

        $class = strtolower(basename($class));

        if ($classPath = $this->getClassPath($class)) {
            include $classPath;
        }

        stopProfile("ShopAutoload");
    }

    /**
     * Loading very base classes. We can do this as we know they exists,
     * moreover even further method code could not execute without them.
     *
     * @param string $class
     *
     * @return string Path to file.
     */
    protected function getClassPath($class)
    {
        $filePath = '';

        if (in_array($class, $this->coreClasses)) {
            $filePath = __DIR__. "/../" . $class . ".php";
        } else {
            $directories = $this->getClassDirectories();
            foreach ($directories as $directory) {
                if (file_exists($directory . $class . '.php')) {
                    $filePath = $directory . $class . '.php';
                    break;
                }
            }
        }

        return $filePath;
    }

    /**
     * Return array with classes paths.
     *
     * @return array
     */
    protected function getClassDirectories()
    {
        if ($this->classDirectories === null) {
            $this->classDirectories = $this->generateDirectories();
        }

        return $this->classDirectories;
    }

    /**
     * Returns array of directories where to search for class.
     *
     * @return array
     */
    protected function generateDirectories()
    {
        $shopBasePath = __DIR__ . "/../../";
        return [
            $shopBasePath . 'Core/',
            $shopBasePath . 'Application/Component/Widget/',
            $shopBasePath . 'Application/Component/',
            $shopBasePath . 'Application/Model/',
            $shopBasePath . 'Application/Controller/',
            $shopBasePath . 'Application/Controller/Admin/',
            $shopBasePath . 'Core/exception/',
            $shopBasePath . 'Core/interface/'
        ];
    }
}
