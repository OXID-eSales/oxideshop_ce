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

/**
 * Autoloader for shop classes.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class oxShopAutoloader
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
        startProfile("oxAutoload");

        $class = strtolower(basename($class));

        if ($classPath = $this->getClassPath($class)) {
            include $classPath;
        }

        stopProfile("oxAutoload");
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
            $filePath = getShopBasePath() . "core/" . $class . ".php";
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
        $shopBasePath = getShopBasePath();
        $aClassDirs = array(
            $shopBasePath . 'core/',
            $shopBasePath . 'application/components/widgets/',
            $shopBasePath . 'application/components/services/',
            $shopBasePath . 'application/components/',
            $shopBasePath . 'application/models/',
            $shopBasePath . 'application/controllers/',
            $shopBasePath . 'application/controllers/admin/',
            $shopBasePath . 'application/controllers/admin/reports/',
            $shopBasePath . 'views/',
            $shopBasePath . 'core/exception/',
            $shopBasePath . 'core/interface/',
            $shopBasePath . 'core/cache/',
            $shopBasePath . 'core/cache/connectors/',
            $shopBasePath . 'admin/reports/',
            $shopBasePath . 'admin/',
            $shopBasePath . 'modules/',
            $shopBasePath
        );

        return $aClassDirs;
    }
}
