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

namespace OxidEsales\EshopCommunity\Core\FileSystem;

/**
 * Wrapper for actions related to file system.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class FileSystem
{
    /**
     * Connect all parameters with backslash to single path.
     * Ensure that no double backslash appears if parameter already ends with backslash.
     *
     * @return string
     */
    public function combinePaths()
    {
        $pathElements = func_get_args();
        foreach ($pathElements as $key => $pathElement) {
            $pathElements[$key] = rtrim($pathElement, DIRECTORY_SEPARATOR);
        }

        return implode(DIRECTORY_SEPARATOR, $pathElements);
    }

    /**
     * Check if file exists and is readable
     *
     * @param string $filePath
     *
     * @return bool
     */
    public function isReadable($filePath)
    {
        return (is_file($filePath) && is_readable($filePath));
    }
}
