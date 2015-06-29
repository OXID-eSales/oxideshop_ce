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
class oxModuleVariablesCache
{

    /**
     * Cache file prefix
     */
    const CACHE_FILE_PREFIX = "config";

    /** @var oxShopIdCalculator */
    private $shopIdCalculator;

    /**
     * @param oxShopIdCalculator $shopIdCalculator
     */
    public function __construct($shopIdCalculator = null)
    {
        $this->shopIdCalculator = $shopIdCalculator;
    }

    /**
     * Returns shop module variable value from cache.
     * This method is independent from oxConfig class and does not use database.
     *
     * @param string $sModuleVarName    Module variable name
     * @param bool   $blSubShopSpecific Indicates should cache be shop specific or not
     *
     * @return string
     */
    public function _getFromCache($sModuleVarName, $blSubShopSpecific = true)
    {
        $sFileName = $this->_getCacheFileName($sModuleVarName, $blSubShopSpecific);
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
     * @param bool   $blSubShopSpecific Indicates should cache be shop specific or not
     */
    public function _setToCache($sVarName, $sValue, $blSubShopSpecific = true)
    {
        $sFileName = $this->_getCacheFileName($sVarName, $blSubShopSpecific);
        file_put_contents($sFileName, serialize($sValue), LOCK_EX);
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
    protected function _getCacheFileName($sModuleVarName, $blSubShopSpecific = true)
    {
        $sShopId = "all";
        if ($blSubShopSpecific) {
            $sShopId = $this->getShopIdCalculator()->getShopId();
        }

        $sDir = $this->_getCacheDir();
        $sVar = strtolower(basename($sModuleVarName));
        $sShop = strtolower(basename($sShopId));

        $sFileName = $sDir . "/" . self::CACHE_FILE_PREFIX . "." . $sShop . '.' . $sVar . ".txt";

        return $sFileName;
    }

    /**
     *
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
     * @param oxShopIdCalculator $shopIdCalculator
     */
    public function setShopIdCalculator($shopIdCalculator)
    {
        $this->shopIdCalculator = $shopIdCalculator;
    }

    /**
     * @return oxShopIdCalculator
     */
    protected function getShopIdCalculator()
    {
        return $this->shopIdCalculator;
    }
}
