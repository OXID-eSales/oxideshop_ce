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
class SubShopSpecificFileCache extends \OxidEsales\Eshop\Core\FileCache
{
    /** @var ShopIdCalculator */
    private $shopIdCalculator;

    /**
     * @param ShopIdCalculator $shopIdCalculator
     */
    public function __construct($shopIdCalculator)
    {
        $this->shopIdCalculator = $shopIdCalculator;
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
        $shopId = strtolower(basename($this->getShopIdCalculator()->getShopId()));

        return parent::CACHE_FILE_PREFIX . ".$shopId.$name.txt";
    }

    /**
     * @return ShopIdCalculator
     */
    protected function getShopIdCalculator()
    {
        return $this->shopIdCalculator;
    }
}
