<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter;

/**
 * @internal
 */
interface ShopAdapterInterface
{
    /**
     * @param string $email
     * @return bool
     */
    public function isValidEmail($email);

    /**
     * @param string $string
     * @return string
     */
    public function translateString($string);

    /**
     * @param string $moduleId
     */
    public function invalidateModuleCache(string $moduleId);

    /**
     * @return array
     */
    public function getModules();
}
