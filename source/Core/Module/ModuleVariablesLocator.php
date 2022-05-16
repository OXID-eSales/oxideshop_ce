<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\FileCache;
use OxidEsales\Eshop\Core\Registry;

/**
 * Selects module variables from database or cache.
 *
 * @internal Do not make a module extension for this class.
 */
class ModuleVariablesLocator
{
    /** @var array Static cache for module information variables. */
    protected static $moduleVariables = [];

    /** @var FileCache */
    private $fileCache;

    /** @var \OxidEsales\Eshop\Core\ShopIdCalculator */
    private $shopIdCalculator;

    /**
     * @param FileCache        $fileCache
     * @param \OxidEsales\Eshop\Core\ShopIdCalculator $shopIdCalculator
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
        self::$moduleVariables = [];
        FileCache::clearCache();
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
        $masterDb = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();

        $shopId = $this->getShopIdCalculator()->getShopId();

        $query = "SELECT oxvarvalue FROM oxconfig WHERE oxvarname = :oxvarname AND oxshopid = :oxshopid";
        $value = $masterDb->getOne($query, [
            ':oxvarname' => $name,
            ':oxshopid'  => $shopId
        ]);

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
     * @return \OxidEsales\Eshop\Core\ShopIdCalculator
     */
    protected function getShopIdCalculator()
    {
        return $this->shopIdCalculator;
    }
}
