<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\FileCache;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ShopConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ActiveClassExtensionChainResolverInterface;
/**
 * Selects module variables from database or cache.
 *
 * @deprecated We don't store module variables in the database. Please use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface instead.
 *
 * @internal   Do not make a module extension for this class.
 */
class ModuleVariablesLocator
{
    /** @var array Static cache for module information variables. */
    protected static $moduleVariables = [];

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

        if ($name === 'aModules') {
            self::$moduleVariables[$name] = $this->getClassExtensionsChain();

            return $this->getClassExtensionsChain();
        }

        $cache = $this->getFileCache();

        //first try to get it from cache
        $value = $cache->getFromCache($name);

        if (is_null($value)) {
            /**
             * @todo we still use this class to get class extensions chain for BC and support of oxTestModules::addFunction().
             *       The whole class should be removed in the future.
             */
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
    public function setModuleVariable($name, $value): void
    {
        if (is_null($value)) {
            self::$moduleVariables = null;
        } else {
            self::$moduleVariables[$name] = $value;
        }

        if ($name !== 'aModules') {
            $this->getFileCache()->setToCache($name, $value);
        }
    }

    /**
     * Resets previously set module information.
     *
     * @static
     */
    public static function resetModuleVariables(): void
    {
        self::$moduleVariables = [];
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
            ':oxshopid'  => $shopId,
        ]);

        return unserialize($value);
    }

    /**
     * @return FileCache
     */
    protected function getFileCache(): FileCache
    {
        return $this->fileCache;
    }

    /**
     * @return ShopIdCalculator
     */
    protected function getShopIdCalculator(): ShopIdCalculator
    {
        return $this->shopIdCalculator;
    }

    private function getClassExtensionsChain(): array
    {
        try {
            $chain = ContainerFactory::getInstance()
                ->getContainer()
                ->get(ActiveClassExtensionChainResolverInterface::class)
                ->getActiveExtensionChain($this->getShopIdCalculator()->getShopId())->getChain();
        } catch (ShopConfigurationNotFoundException $exception) {
            $chain = [];
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        }

        return $chain;
    }
}
