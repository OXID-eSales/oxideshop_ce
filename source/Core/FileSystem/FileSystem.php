<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\FileSystem;

/**
 * Wrapper for actions related to file system.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
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
