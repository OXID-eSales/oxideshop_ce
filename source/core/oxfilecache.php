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
 * Cache for storing module variables selected from database.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class oxFileCache
{
    /**
     * Cache file prefix
     */
    const CACHE_FILE_PREFIX = "config";

    /**
     * Returns shop module variable value from cache.
     * This method is independent from oxConfig class and does not use database.
     *
     * @param string $sModuleVarName    Module variable name
     *
     * @return string
     */
    public function _getFromCache($sModuleVarName)
    {
        $sFileName = $this->_getCacheFileName($sModuleVarName);
        $sValue = null;
        if (is_readable($sFileName)) {
            $sValue = file_get_contents($sFileName);
            if ($sValue == serialize(false)) {
                return false;
            }

            $sValue = unserialize($sValue);
            if ($sValue === false) {
                $sValue = null;
            }
        }

        return $sValue;
    }

    /**
     * Writes shop module variable information to cache.
     *
     * @param string $sVarName          Variable name
     * @param string $sValue            Variable value.
     */
    public function _setToCache($sVarName, $sValue)
    {
        $sFileName = $this->_getCacheFileName($sVarName);
        file_put_contents($sFileName, serialize($sValue), LOCK_EX);
    }

    /**
     * Clears all cache.
     */
    public static function clearCache()
    {
        $sMask = oxRegistry::get("oxConfigFile")->getVar("sCompileDir") . "/" . self::CACHE_FILE_PREFIX . ".*.txt";
        $aFiles = glob($sMask);
        if (is_array($aFiles)) {
            foreach ($aFiles as $sFile) {
                if (is_file($sFile)) {
                    @unlink($sFile);
                }
            }
        }
    }

    /**
     * Gets cache directory
     *
     * @return string
     */
    protected function _getCacheDir()
    {
        $sDir = oxRegistry::get("oxConfigFile")->getVar("sCompileDir");

        return $sDir;
    }

    /**
     * Returns module file cache name.
     *
     * @param string $sModuleVarName    Module variable name
     * @param bool   $blSubShopSpecific Shop id
     *
     * @return string
     */
    protected function _getCacheFileName($sModuleVarName)
    {
        $sDir = $this->_getCacheDir();
        $sVar = strtolower(basename($sModuleVarName));
        $sShop = strtolower(basename($this->getShopId()));

        $sFileName = $sDir . "/" . self::CACHE_FILE_PREFIX . "." . $sShop . '.' . $sVar . ".txt";

        return $sFileName;
    }

    /**
     * Returns shopId which should be used for cache file name generation.
     *
     * @return string
     */
    protected function getShopId()
    {
        return "all";
    }
}
