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

namespace OxidEsales\Eshop\Core;

/**
 * Selects module variables from database or cache.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleVariablesLocator
{
    /** @var array Static cache for module information variables. */
    protected static $moduleVariables = array();

    /** @var FileCache */
    private $fileCache;

    /** @var ShopIdCalculator */
    private $shopIdCalculator;

    /**
     * @param FileCache        $fileCache
     * @param ShopIdCalculator $shopIdCalculator
     */
    public function __construct($fileCache, $shopIdCalculator)
    {
        $this->fileCache = $fileCache;
        $this->shopIdCalculator = $shopIdCalculator;
    }

    /**
     * Retrieves module configuration variable for the base shop.
     * Currently getModuleVar() is expected to be called with one of the values: aModules | aDisabledModules | aModulePaths
     * This method is independent from oxConfig functionality.
     *
     * @param string $name Configuration array name
     *
     * @return array
     */
    public function getModuleVariable($name)
    {
        if (isset(self::$moduleVariables[$name])) {
            return self::$moduleVariables[$name];
        }
        $cache = $this->getFileCache();

        //first try to get it from cache
        $value = $cache->getFromCache($name);

        if (is_null($value)) {
            $value = $this->getModuleVarFromDB($name);
            $cache->setToCache($name, $value);
        }

        self::$moduleVariables[$name] = $value;

        return $value;
    }

    /**
     * Sets module information variable. The variable is set statically and is not saved for future.
     *
     * @param string $name  Configuration array name
     * @param array  $value Module name values
     */
    public function setModuleVariable($name, $value)
    {
        if (is_null($value)) {
            self::$moduleVariables = null;
        } else {
            self::$moduleVariables[$name] = $value;
        }

        $this->getFileCache()->setToCache($name, $value);
    }

    /**
     * Resets previously set module information.
     *
     * @static
     */
    public static function resetModuleVariables()
    {
        self::$moduleVariables = array();
        FileCache::clearCache();
    }

    /**
     * Returns configuration key. This method is independent from oxConfig functionality.
     *
     * @return string
     */
    protected function getConfigurationKey()
    {
        if (Registry::instanceExists('oxConfigFile')) {
            $config = Registry::get('oxConfigFile');
        } else {
            $config = new ConfigFile(getShopBasePath() . '/config.inc.php');
            Registry::set('oxConfigFile', $config);
        }
        return $config->getVar('sConfigKey') ?: Config::DEFAULT_CONFIG_KEY;
    }

    /**
     * Returns shop module variable value directly from database.
     *
     * @param string $name Module variable name
     *
     * @return string
     */
    protected function getModuleVarFromDB($name)
    {
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = Database::getMaster();

        $shopId = $this->getShopIdCalculator()->getShopId();
        $configKey = $this->getConfigurationKey();

        $query = "SELECT DECODE( oxvarvalue , ? ) FROM oxconfig WHERE oxvarname = ? AND oxshopid = ?";

        $value = $masterDb->getOne($query, array($configKey, $name, $shopId));

        return unserialize($value);
    }

    /**
     * @return FileCache
     */
    protected function getFileCache()
    {
        return $this->fileCache;
    }

    /**
     * @return ShopIdCalculator
     */
    protected function getShopIdCalculator()
    {
        return $this->shopIdCalculator;
    }
}
