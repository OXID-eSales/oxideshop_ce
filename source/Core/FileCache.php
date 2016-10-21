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

namespace OxidEsales\EshopCommunity\Core;

use oxRegistry;

/**
 * Cache for storing module variables selected from database.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class FileCache
{
    /** Cache file prefix */
    const CACHE_FILE_PREFIX = "config";

    /**
     * Returns cached item value by given key.
     * This method is independent from oxConfig class and does not use database.
     *
     * @param string $key cached item key.
     *
     * @return mixed
     */
    public function getFromCache($key)
    {
        $fileName = $this->getCacheFilePath($key);
        $value = null;
        if (is_readable($fileName)) {
            $value = file_get_contents($fileName);
            if ($value == serialize(false)) {
                return false;
            }

            $value = unserialize($value);
            if ($value === false) {
                $value = null;
            }
        }

        return $value;
    }

    /**
     * Caches item value by given key.
     *
     * @param string $key   cached item key.
     * @param mixed  $value
     */
    public function setToCache($key, $value)
    {
        $fileName = $this->getCacheFilePath($key);
        file_put_contents($fileName, serialize($value), LOCK_EX);
    }

    /**
     * Clears all cache by deleting cached files.
     */
    public static function clearCache()
    {
        $tempDirectory = oxRegistry::get("oxConfigFile")->getVar("sCompileDir");
        $mask = $tempDirectory . "/" . self::CACHE_FILE_PREFIX . ".*.txt";
        $files = glob($mask);
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }

    /**
     * Returns module file cache name.
     *
     * @param string $key cached item key. Will be used for file name generation.
     *
     * @return string
     */
    protected function getCacheFilePath($key)
    {
        return $this->getCacheDir() . "/" . $this->getCacheFileName($key);
    }

    /**
     * Returns cache directory.
     *
     * @return string
     */
    protected function getCacheDir()
    {
        return oxRegistry::get("oxConfigFile")->getVar("sCompileDir");
    }

    /**
     * Returns shopId which should be used for cache file name generation.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getCacheFileName($key)
    {
        $name = strtolower(basename($key));

        return self::CACHE_FILE_PREFIX .".all.$name.txt";
    }
}
