<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Cache for storing module variables selected from database.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
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
        $cacheDirectory = $this->getCacheDir();

        $tmpFile = $cacheDirectory . "/" . basename($fileName) . uniqid('.temp', true) . '.txt';
        file_put_contents($tmpFile, serialize($value), LOCK_EX);

        rename($tmpFile, $fileName);
    }

    /**
     * Clears all cache by deleting cached files.
     */
    public static function clearCache()
    {
        $tempDirectory = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class)->getVar("sCompileDir");
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
        return \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class)->getVar("sCompileDir");
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
