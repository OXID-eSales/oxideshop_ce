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
 * Selects variables from database.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class oxModuleVariablesLocator
{
    /**
     * Module information variables
     *
     * @var array
     */
    protected static $_aModuleVars = array();

    /** @var oxModuleVariablesCache */
    private $variablesCache;

    /** @var oxShopIdCalculator */
    private $shopIdCalculator;

    /**
     * @param oxModuleVariablesCache $variablesCache
     * @param oxShopIdCalculator     $shopIdCalculator
     */
    public function __construct($variablesCache, $shopIdCalculator)
    {
        $this->variablesCache = $variablesCache;
        $this->shopIdCalculator = $shopIdCalculator;
    }

    /**
     * Retrieves module configuration variable for the base shop.
     * Currently getModuleVar() is expected to be called with one of the values: aModules | aDisabledModules | aModulePaths
     * This method is independent from oxConfig functionality.
     *
     * @param string $sModuleVarName Configuration array name
     *
     * @return array
     */
    public function getModuleVar($sModuleVarName)
    {
        //static cache
        if (isset(self::$_aModuleVars[$sModuleVarName])) {
            return self::$_aModuleVars[$sModuleVarName];
        }
        $cache = $this->getVariablesCache();

        //first try to get it from cache
        //we do not use any of our cache APIs, as we want to prevent any class dependencies here
        $aValue = $cache->_getFromCache($sModuleVarName);

        if (is_null($aValue)) {
            $aValue = $this->_getModuleVarFromDB($sModuleVarName);
            $cache->_setToCache($sModuleVarName, $aValue);
        }

        //static cache
        self::$_aModuleVars[$sModuleVarName] = $aValue;

        return $aValue;
    }

    /**
     * Sets module information variable. The variable is set statically and is not saved for future.
     *
     * @param string $sModuleVarName Configuration array name
     * @param array  $aValues        Module name values
     */
    public function setModuleVar($sModuleVarName, $aValues)
    {
        if (is_null($aValues)) {
            self::$_aModuleVars = null;
        } else {
            self::$_aModuleVars[$sModuleVarName] = $aValues;
        }

        $this->getVariablesCache()->_setToCache($sModuleVarName, $aValues);
    }

    /**
     * Resets previously set module information.
     *
     * @static
     */
    public static function resetModuleVars()
    {
        self::$_aModuleVars = array();
        oxModuleVariablesCache::clearCache();
    }

    /**
     * Returns configuration key. This method is independent from oxConfig functionality.
     *
     * @return string
     */
    protected function _getConfKey()
    {
        $sFileName = dirname(__FILE__) . "/oxconfk.php";
        $sCfgFile = new oxConfigFile($sFileName);

        return $sCfgFile->getVar("sConfigKey");
    }

    /**
     * Returns shop module variable value directly from database.
     *
     * @param string $sModuleVarName Module variable name
     *
     * @return string
     */
    protected function _getModuleVarFromDB($sModuleVarName)
    {
        $oDb = oxDb::getDb();

        $sShopId = $this->getShopIdCalculator()->getShopId();
        $sConfKey = $this->_getConfKey();

        $sSelect = "SELECT DECODE( oxvarvalue , " . $oDb->quote($sConfKey) . " ) FROM oxconfig " .
            "WHERE oxvarname = " . $oDb->quote($sModuleVarName) . " AND oxshopid = " . $oDb->quote($sShopId);

        $sModuleVarName = $oDb->getOne($sSelect, false, false);

        $sModuleVarName = unserialize($sModuleVarName);

        return $sModuleVarName;
    }

    /**
     * @return oxModuleVariablesCache
     */
    protected function getVariablesCache()
    {
        return $this->variablesCache;
    }

    /**
     * @return oxShopIdCalculator
     */
    protected function getShopIdCalculator()
    {
        return $this->shopIdCalculator;
    }
}
